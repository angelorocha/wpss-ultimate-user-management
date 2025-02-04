<?php

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSRoles
 * @since 1.0.0
 */
class WPSSRoles {
	
	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;
	
	/**
	 * Get available roles
	 * @since 1.0.0
	 * @var object|null
	 */
	private static ?object $get_roles = null;
	
	/**
	 * Set roles to exclude from editor
	 * @since 1.0.0
	 * @var array
	 */
	private static array $roles_filter = [
		'administrator',
		'editor',
		'author',
		'contributor',
		'subscriber',
		'vip_support',
		'vip_support_inactive',
	];
	
	/**
	 * Default role, move users from a deleted role to this
	 * @since 1.0.0
	 * @var string
	 */
	private static string $default_role = 'subscriber';
	
	public function __construct() {
		/** Ajax calls to add role */
		add_action( 'wp_ajax_wpss_add_roles_action', [ $this, 'add_role_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_add_roles_action', [ $this, 'add_role_action' ] );
		
		/** Ajax calls to remove role */
		add_action( 'wp_ajax_wpss_remove_role_action', [ $this, 'remove_role_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_remove_role_action', [ $this, 'remove_role_action' ] );
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
	 * Add role ajax action
	 * @since 1.0.0
	 */
	#[NoReturn] public static function add_role_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$get_role_data = WPSSPostGet::post( 'role' );
		parse_str( $get_role_data, $role_key );
		$instance        = self::instance();
		$add_role_action = $instance->add_role( $role_key['wpss-add-new-role'] );
		if ( !is_null( $add_role_action ) ) {
			/* Translators: %s is a role name */
			echo sprintf( esc_html__( 'Role %s successfully added!', 'wpss-ultimate-user-management' ), esc_html( $add_role_action['name'] ) );
		} else {
			echo esc_html__( 'Failed to create role', 'wpss-ultimate-user-management' );
		}
		exit;
	}
	
	/**
	 * Remove role ajax action
	 * @since 1.0.0
	 */
	#[NoReturn] public static function remove_role_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$instance = self::instance();
		$roleId   = WPSSPostGet::post( 'role_id' );
		/** Don't remove WordPress native roles */
		if ( !in_array( $roleId, self::$roles_filter ) ) {
			/** If user was only a removed role, move him to default role */
			$instance->move_users_without_role_to_default_role( $roleId );
			$remove_action = $instance->remove_role( sanitize_text_field( $roleId ) );
			if ( $remove_action ) {
				$get_role_name = WPSSPostGet::post( 'role_name' );
				/* Translators: %s is a role name */
				echo sprintf( esc_html__( 'Role %s successfully removed!', 'wpss-ultimate-user-management' ), esc_html( $get_role_name ) );
			} else {
				echo esc_html__( 'Failed to remove role', 'wpss-ultimate-user-management' );
			}
		}
		exit;
	}
	
	/**
	 * Return array key/val of available roles excluding default WordPress roles
	 *
	 * @param bool $filter select if roles are filtered, default is true
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_roles_names( bool $filter = true ): array {
		$roles = self::instance()->get_roles()->role_names;
		$roles = array_map( 'translate_user_role', $roles );
		if ( $filter ) {
			$filter = self::$roles_filter;
			
			return array_filter( $roles, function( $role ) use ( $filter ) {
				if ( in_array( $role, $filter ) ) {
					return false;
				}
				
				return $role;
			},                   ARRAY_FILTER_USE_KEY );
		}
		
		return $roles;
	}
	
	/**
	 * Define a default role when user have no roles
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function wpss_set_default_role(): string {
		$role = WPSSPluginHelper::get_option( 'wpss_default_role' );
		if ( !empty( $role ) ) {
			self::$default_role = $role;
		}
		
		return self::$default_role;
	}
	
	/**
	 * If user have no role, move user to default role
	 *
	 * @param string $role
	 *
	 * @since 1.0.0
	 */
	private function move_users_without_role_to_default_role( string $role ): void {
		$users_in_role = WPSSUsers::get_users_from_role( $role );
		if ( !empty( $users_in_role ) ) {
			foreach ( $users_in_role as $user ) {
				if ( in_array( $role, $user->roles ) ) {
					get_userdata( $user->ID )->remove_role( sanitize_text_field( $role ) );
					get_userdata( $user->ID )->remove_cap( sanitize_text_field( $role ) );
					if ( count( $user->roles ) === 1 ) {
						get_userdata( $user->ID )->add_role( sanitize_text_field( self::instance()->wpss_set_default_role() ) );
					}
				}
			}
		}
	}
	
	/**
	 * Add a new role if not exists
	 *
	 * @param string $role
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public function add_role( string $role ): ?array {
		$role_name = sanitize_text_field( $role );
		$role_key  = sanitize_title( $role );
		if ( empty( $role_key ) || in_array( $role_key, array_keys( self::get_roles_names() ) ) ) {
			return null;
		}
		add_role( $role_key, $role_name, [ 'read' => true ] );
		
		return [ 'name' => $role_name, 'key' => $role_key ];
	}
	
	/**
	 * Remove role by role slug
	 *
	 * @param string $role
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function remove_role( string $role ): bool {
		if ( in_array( $role, array_keys( self::instance()->get_roles_names() ) ) ) {
			remove_role( $role );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get available roles
	 * @return object
	 * @since 1.0.0
	 */
	public function get_roles(): object {
		global $wp_roles;
		if ( is_null( self::$get_roles ) ) {
			self::$get_roles = $wp_roles;
		}
		
		return self::$get_roles;
	}
}
