<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

use JetBrains\PhpStorm\NoReturn;
use WP_User;
use WP_User_Query;

/**
 * Class WPSSUsers
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
	
	public function __construct() {
		
		/** Ajax calls to get user details */
		add_action( 'wp_ajax_wpss_get_user_details_action', [ $this, 'get_user_details_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_get_user_details_action', [ $this, 'get_user_details_action' ] );
		
		/** Ajax call to set user roles */
		add_action( 'wp_ajax_wpss_set_user_roles_action', [ $this, 'set_user_roles_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_set_user_roles_action', [ $this, 'set_user_roles_action' ] );
		
		/** If exists, add specific roles to new users */
		add_action( 'user_register', [ $this, 'add_user_role_on_register' ] );
	}
	
	/**
	 * Get class instance
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public static function instance(): object {
		if ( is_null( self::$instance ) ):
			self::$instance = new self();
		endif;
		
		return self::$instance;
	}
	
	/**
	 * Get user details ajax call
	 * @since 1.0.0
	 */
	#[NoReturn] public static function get_user_details_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$instance = self::instance();
		$user_data = $instance->get_user( WPSSPostGet::post( 'user_id' ) );
		if ( ! is_null( $user_data ) ):
			$template = [
				'template'  => 'content/user-details',
				'user_data' => $user_data,
			];
			WPSSAdminFrontend::render_template( $template );
		endif;
		exit;
	}
	
	/**
	 * Set user roles ajax call
	 * @since 1.0.0
	 */
	#[NoReturn] public static function set_user_roles_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$get_user_role_data = WPSSPostGet::post( 'user_roles' );
		parse_str( $get_user_role_data, $new_roles );
		$user_id = WPSSPostGet::post( 'user_id' );
		$instance = self::instance();
		$current_user_roles = $instance->get_user( $user_id )['user_roles'];
		$remove_roles = array_diff( $current_user_roles, (array) $new_roles['wpss-add-role-to-user'] );
		$instance->add_user_roles( $user_id, (array) $new_roles['wpss-add-role-to-user'] );
		$instance->remove_user_roles( $user_id, $remove_roles );
		exit;
	}
	
	/**
	 * For new users, add specific roles
	 *
	 * @param int $user_id
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
	 * Add roles to user
	 *
	 * @param int $user_id
	 * @param array $roles
	 * @param bool $echo
	 *
	 * @since 1.0.0
	 */
	private function add_user_roles( int $user_id, array $roles, bool $echo = true ): void {
		$current_user_roles = self::get_user( $user_id )['user_roles'];
		$output = [];
		if ( ! empty( $user_id ) && ! empty( $roles ) ):
			foreach ( $roles as $role ):
				if ( ! in_array( $role, $current_user_roles ) ):
					self::get_user_object( $user_id )->add_role( sanitize_text_field( $role ) );
					$output[] = WPSSRoles::get_roles_names( false )[ $role ];
				endif;
			endforeach;
		endif;
		if ( ! empty( $output ) && $echo ):
			$count = count( $output );
			WPSSAdminFrontend::render_template(
				[
					'template' => 'messages/user-role-add',
					'args'     => [ $count, $output ],
				]
			);
		endif;
	}
	
	/**
	 * Remove roles from user
	 *
	 * @param int $user_id
	 * @param array $roles
	 *
	 * @since 1.0.0
	 */
	private function remove_user_roles( int $user_id, array $roles ): void {
		$output = [];
		if ( ! empty( $roles ) ):
			foreach ( $roles as $role ):
				self::get_user_object( $user_id )->remove_role( sanitize_text_field( $role ) );
				self::get_user_object( $user_id )->remove_cap( sanitize_text_field( $role ) );
				$output[] = WPSSRoles::get_roles_names( false )[ $role ];
			endforeach;
		endif;
		if ( ! empty( $output ) ):
			$count = count( $output );
			WPSSAdminFrontend::render_template(
				[
					'template' => 'messages/user-role-remove',
					'args'     => [ $count, $output ],
				]
			);
		endif;
	}
	
	/**
	 * Get user list
	 *
	 * @param int $limit
	 * @param string $search
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_users( int $limit = 10, string $search = '' ): array {
		$rpp = $limit;
		$get_page = WPSSPostGet::get( 'cpage' );
		$page = ! empty( $get_page ) ? (int) $get_page : 1;
		$offset = ( $page * $rpp ) - $rpp;
		$args['number'] = $limit;
		$args['offset'] = $offset;
		if ( ! empty( $search ) ):
			$args['search'] = "*$search*";
		endif;
		
		$users = [];
		foreach ( self::instance()->user_query( $args )->get_results() as $user ):
			$users[ $user->ID ] = get_userdata( $user->ID )->display_name;
		endforeach;
		
		$users['total'] = self::instance()->user_query( $args )->get_total();
		
		return $users;
	}
	
	/**
	 * @param int $rpp results per page
	 * @param int $total get total of pages
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public static function paginate_users( int $rpp, int $total ): ?string {
		$get_page = WPSSPostGet::get( 'cpage' );
		$page = ! empty( $get_page ) ? (int) $get_page : 1;
		$args = ! empty( $get_page ) ? [
			'cpage'  => '%#%',
			'search' => WPSSPostGet::get( 'search' ),
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
	 * List all users from specific role
	 *
	 * @param string $role
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_users_from_role( string $role ): array {
		$args['role'] = $role;
		
		return self::instance()->user_query( $args )->get_results();
	}
	
	/**
	 * Get user details
	 *
	 * @param int $user_id
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public function get_user( int $user_id ): ?array {
		if ( ! $user_id && ! get_userdata( $user_id ) ):
			return null;
		endif;
		$user = get_userdata( $user_id );
		
		return [
			'user_login'      => $user->user_login,
			'user_email'      => $user->user_email,
			'user_registered' => gmdate( __( 'Y-m-d H:i:s', 'wpss-ultimate-user-management' ), strtotime( $user->user_registered ) ),
			'user_roles'      => $user->roles,
		];
	}
	
	/**
	 * Get WordPress User object
	 *
	 * @param int $user_id
	 *
	 * @return WP_User
	 * @since 1.0.0
	 */
	public function get_user_object( int $user_id ): WP_User {
		return new WP_User( $user_id );
	}
	
	/**
	 * Get all users
	 *
	 * @param array $args
	 *
	 * @return WP_User_Query
	 * @since 1.0.0
	 */
	public function user_query( array $args = [] ): WP_User_Query {
		if ( ! isset( $args['number'] ) ):
			$args['number'] = - 1;
		endif;
		
		if ( ! isset( $args['orderby'] ) ):
			$args['orderby'] = 'ID';
		endif;
		
		if ( ! isset( $args['order'] ) ):
			$args['order'] = 'DESC';
		endif;
		
		if ( is_multisite() ):
			$args['blog_id'] = get_current_blog_id();
		endif;
		
		return new WP_User_Query( $args );
	}
}
