<?php
/**
 * Manages per-user role assignment and the users list screen.
 *
 * @package wpss-ultimate-user-management
 */

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use JetBrains\PhpStorm\NoReturn;
use WP_User;
use WP_User_Query;

/**
 * Class WPSSUsers
 *
 * @since 1.0.0
 */
class WPSSUsers {

	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;

	/**
	 * Register the user-management AJAX actions and hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/** Ajax calls to get user details */
		add_action( 'wp_ajax_wpss_get_user_details_action', [ $this, 'get_user_details_action' ] );

		/** Ajax call to set user roles */
		add_action( 'wp_ajax_wpss_set_user_roles_action', [ $this, 'set_user_roles_action' ] );

		/** If exists, add specific roles to new users */
		add_action( 'user_register', [ $this, 'add_user_role_on_register' ] );

		/** Render a new link on WordPress user list page */
		add_filter( 'user_row_actions', [ $this, 'add_role_link_on_user_list' ], 10, 2 );

		/** Open user roles box */
		add_action( 'wp_ajax_openUserRolesBox', [ $this, 'open_user_roles_box' ] );
	}

	/**
	 * Get class instance
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public static function instance(): object {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Render the role-assignment box for a single user.
	 *
	 * @since 1.0.0
	 */
	#[NoReturn] public function open_user_roles_box(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		if ( current_user_can( 'manage_options' ) ) {
			$nonce   = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
			$user_id = RoleCraftRequest::post( 'user_id', false, $nonce );
			WPSSAdminFrontend::render(
				[
					'template' => 'content/user-roles-box',
					'args'     => $user_id,
				]
			);
		}
		wp_die();
	}

	/**
	 * Show add role link on native WordPress list user page.
	 *
	 * @param array    $actions Existing row actions.
	 * @param WP_User $user_object User the row belongs to.
	 * @return array
	 * @since 1.2.0
	 */
	public function add_role_link_on_user_list( array $actions, WP_User $user_object ): array {
		if ( current_user_can( 'manage_options' ) ) {
			$link_args = [
				'template' => 'content/edit-user-link',
				'args'     => $user_object,
			];
			ob_start();
			WPSSAdminFrontend::render( $link_args );
			$render                           = ob_get_clean();
			$actions['rolecraft_permissions'] = $render;
		}

		return $actions;
	}

	/**
	 * Get user details ajax call
	 *
	 * @since 1.0.0
	 */
	#[NoReturn]
	public static function get_user_details_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$instance  = self::instance();
		$nonce     = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
		$user_data = $instance->get_user( RoleCraftRequest::post( 'user_id', false, $nonce ) );
		if ( ! is_null( $user_data ) ) {
			$template = [
				'template'  => 'content/user-details',
				'user_data' => $user_data,
			];
			WPSSAdminFrontend::render( $template );
		}
		exit;
	}

	/**
	 * Set user roles ajax call
	 *
	 * @since 1.0.0
	 */
	#[NoReturn]
	public static function set_user_roles_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$nonce              = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
		$get_user_role_data = RoleCraftRequest::post( 'user_roles', false, $nonce );
		parse_str( $get_user_role_data, $new_roles );
		$user_id            = RoleCraftRequest::post( 'user_id', false, $nonce );
		$instance           = self::instance();
		$current_user_roles = $instance->get_user( $user_id )['user_roles'];
		if ( empty( $new_roles['wpss-add-role-to-user'] ) ) {
			$new_roles['wpss-add-role-to-user'][] = WPSSPluginHelper::get_option( 'wpss_default_role' );
		}
		$remove_roles = array_diff( $current_user_roles, (array) $new_roles['wpss-add-role-to-user'] );
		$instance->add_user_roles( $user_id, (array) $new_roles['wpss-add-role-to-user'] );
		$instance->remove_user_roles( $user_id, $remove_roles );
		exit;
	}

	/**
	 * For new users, add specific roles.
	 *
	 * @param int $user_id Newly registered user id.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_user_role_on_register( int $user_id ): void {
		$new_users_roles = WPSSPluginHelper::get_option( 'wpss_roles_to_new_users' );
		if ( ! empty( $new_users_roles ) ) {
			$new_users_roles = json_decode( $new_users_roles, true );
			$new_users_roles = array_map( 'sanitize_text_field', $new_users_roles );
			self::add_user_roles( $user_id, $new_users_roles, false );
		}
	}

	/**
	 * Add roles to user.
	 *
	 * @param int   $user_id User to add roles to.
	 * @param array $roles Role slugs to add.
	 * @param bool  $notify Whether to render the "role added" success message.
	 *
	 * @since 1.0.0
	 */
	private function add_user_roles( int $user_id, array $roles, bool $notify = true ): void {
		$current_user_roles = self::get_user( $user_id )['user_roles'];
		$output             = [];
		if ( ! empty( $user_id ) && ! empty( $roles ) ) {
			foreach ( $roles as $role ) {
				if ( ! in_array( $role, $current_user_roles, true ) ) {
					self::get_user_object( $user_id )->add_role( sanitize_text_field( $role ) );
					$output[] = WPSSRoles::get_roles_names( false )[ $role ];
				}
			}
		}
		if ( ! empty( $output ) && $notify ) {
			$count = count( $output );
			WPSSAdminFrontend::render(
				[
					'template' => 'messages/user-role-add',
					'args'     => [ $count, $output ],
				]
			);
		}
	}

	/**
	 * Remove roles from user.
	 *
	 * @param int   $user_id User to remove roles from.
	 * @param array $roles Role slugs to remove.
	 *
	 * @since 1.0.0
	 */
	private function remove_user_roles( int $user_id, array $roles ): void {
		$output = [];
		if ( ! empty( $roles ) ) {
			foreach ( $roles as $role ) {
				self::get_user_object( $user_id )->remove_role( sanitize_text_field( $role ) );
				self::get_user_object( $user_id )->remove_cap( sanitize_text_field( $role ) );
				$output[] = WPSSRoles::get_roles_names( false )[ $role ];
			}
		}
		if ( ! empty( $output ) ) {
			$count = count( $output );
			WPSSAdminFrontend::render(
				[
					'template' => 'messages/user-role-remove',
					'args'     => [ $count, $output ],
				]
			);
		}
	}

	/**
	 * Get user list.
	 *
	 * @param int    $limit Results per page.
	 * @param string $search Search term.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_users( int $limit = 10, string $search = '' ): array {
		$rpp            = $limit;
		$nonce          = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
		$get_page       = RoleCraftRequest::get( 'cpage', $nonce );
		$page           = ! empty( $get_page ) ? (int) $get_page : 1;
		$offset         = ( $page * $rpp ) - $rpp;
		$args['number'] = $limit;
		$args['offset'] = $offset;
		if ( ! empty( $search ) ) {
			$args['search'] = "*$search*";
		}

		$users = [];
		foreach ( self::instance()->user_query( $args )->get_results() as $user ) {
			$users[ $user->ID ] = get_userdata( $user->ID )->display_name;
		}

		$users['total'] = self::instance()->user_query( $args )->get_total();

		return $users;
	}

	/**
	 * Render pagination links for the users table.
	 *
	 * @param int $rpp Results per page.
	 * @param int $total Total number of matching users.
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public static function paginate_users( int $rpp, int $total ): ?string {
		$nonce    = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
		$get_page = RoleCraftRequest::get( 'cpage', $nonce );
		$page     = ! empty( $get_page ) ? (int) $get_page : 1;
		$args     = ! empty( $get_page ) ? [
			'cpage'  => '%#%',
			'search' => RoleCraftRequest::get( 'search', $nonce ),
		] : [ 'cpage' => '%#%' ];

		return paginate_links(
			[
				'base'      => add_query_arg( $args ),
				'format'    => '',
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'total'     => ceil( $total / $rpp ),
				'current'   => $page,
			]
		);
	}

	/**
	 * List all users from specific role.
	 *
	 * @param string $role Role slug.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_users_from_role( string $role ): array {
		$args['role'] = $role;

		return self::instance()->user_query( $args )->get_results();
	}

	/**
	 * Get user details.
	 *
	 * @param int $user_id User id.
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public function get_user( int $user_id ): ?array {
		if ( ! $user_id && ! get_userdata( $user_id ) ) {
			return null;
		}
		$user = get_userdata( $user_id );

		return [
			'user_login'      => $user->user_login,
			'user_email'      => $user->user_email,
			'user_registered' => gmdate( __( 'Y-m-d H:i:s', 'wpss-ultimate-user-management' ), strtotime( $user->user_registered ) ),
			'user_roles'      => $user->roles,
		];
	}

	/**
	 * Get WordPress User object.
	 *
	 * @param int $user_id User id.
	 *
	 * @return WP_User
	 * @since 1.0.0
	 */
	public function get_user_object( int $user_id ): WP_User {
		return new WP_User( $user_id );
	}

	/**
	 * Get all users.
	 *
	 * @param array $args WP_User_Query args.
	 *
	 * @return WP_User_Query
	 * @since 1.0.0
	 */
	public function user_query( array $args = [] ): WP_User_Query {
		if ( ! isset( $args['number'] ) ) {
			$args['number'] = -1;
		}

		if ( ! isset( $args['orderby'] ) ) {
			$args['orderby'] = 'ID';
		}

		if ( ! isset( $args['order'] ) ) {
			$args['order'] = 'DESC';
		}

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		return new WP_User_Query( $args );
	}
}
