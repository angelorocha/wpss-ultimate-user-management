<?php

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

/**
 * Class WPSSAdminPages
 * @since 1.0.0
 */
class WPSSAdminPages {
	
	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;
	
	/**
	 * Get all admin menu pages
	 * @since 1.0.0
	 * @var array
	 */
	private static array $get_menus = [];
	
	/**
	 * WordPress option to control admin menus access
	 * @since 1.0.0
	 * @var string
	 */
	private static string $admin_menu_perms_option = 'wpss_admin_menu_access';
	
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'get_menu_list' ] );
		add_action( 'admin_init', [ $this, 'remove_menu_items_from_role' ], 20 );
		
		add_action( 'wp_ajax_menage_admin_menu_options_action', [ $this, 'insert_options_action' ] );
		add_action( 'wp_ajax_nopriv_menage_admin_menu_options_action', [ $this, 'insert_options_action' ] );
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
	 * Insert options action
	 * @since 1.0.0
	 */
	#[NoReturn] public static function insert_options_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$get_data = WPSSPostGet::post('wpss_admin_menus');
		parse_str( $get_data, $menu_data );
		$key = wp_strip_all_tags( $menu_data['wpss-get-role-to-remove-menu'] );
		$val = [];
		if ( ! empty( $menu_data['wpss-show-menu-item'] ) ) {
			$val = array_map( 'wp_strip_all_tags', (array) $menu_data['wpss-show-menu-item'] );
		}
		$format_data = [ $key => $val ];
		if ( empty( $key ) ):
			echo esc_html__( 'Select a valid role', 'wpss-ultimate-user-management' );
			exit;
		endif;
		self::instance()->set_option( wp_json_encode( $format_data ) );
		echo esc_html__( 'Options updated successfully', 'wpss-ultimate-user-management' );
		exit;
	}
	
	/**
	 * Remove admin menus from a role
	 * @since 1.0.0
	 */
	public function remove_menu_items_from_role(): void {
		if ( ! empty( self::get_option() ) ):
			global $menu;
			foreach ( self::get_option() as $get_role => $get_menu ):
				if ( current_user_can( $get_role ) && ! is_super_admin() ):
					foreach ( $get_menu as $remove_menu ):
						/** @var array $menu Avoid php warnings, related bug
						 * here: https://core.trac.wordpress.org/ticket/23767
						 * Some menus are not removed in the admin_menu hook, to
						 * solve this problem this method is linked to the admin_init hook.
						 */
						$menu[] = $remove_menu;
						remove_menu_page( $remove_menu );
					endforeach;
				endif;
			endforeach;
		endif;
	}
	
	/**
	 * Get filtered admin menus
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_menu_list(): array {
		/** @var  $menus
		 * Key 0: Menu name
		 * Key 1: Menu capabilities
		 * Key 2: Menu ID (used to unset)
		 */
		$menus = self::instance()->get_admin_menu();
		$get_menus = [];
		foreach ( $menus as $menu ):
			if ( $menu[1] !== 'read' ):
				/** @var array $menu_title remove menu notifications from option title */
				preg_match( '/(?<=^|>).*?(?=<|$)/s', $menu[0], $menu_title );
				$get_menus[ $menu[2] ] = esc_attr( $menu_title[0] );
			endif;
		endforeach;
		
		return $get_menus;
	}
	
	/**
	 * Get option value
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_option(): array {
		$instance = self::instance();
		$output = [];
		if ( $instance->option_exists() ):
			$output = json_decode( WPSSPluginHelper::get_option( self::$admin_menu_perms_option ), true );
		endif;
		
		return $output;
	}
	
	/**
	 * Set access options
	 *
	 * @param string $value
	 *
	 * @since 1.0.0
	 */
	public function set_option( string $value ): void {
		if ( ! self::option_exists() ):
			WPSSPluginHelper::add_option( self::$admin_menu_perms_option, $value );
		else:
			self::update_option( $value );
		endif;
	}
	
	/**
	 * Update access options
	 *
	 * @param string $update
	 *
	 * @since 1.0.0
	 */
	public function update_option( string $update ): void {
		$update_data = self::get_option();
		$get_data = json_decode( $update, true );
		foreach ( $get_data as $key => $val ):
			$update_data[ $key ] = $val;
			if ( empty( $update_data[ $key ] ) ):
				unset( $update_data[ $key ] );
			endif;
		endforeach;
		WPSSPluginHelper::update_option( self::$admin_menu_perms_option, wp_json_encode( $update_data ) );
	}
	
	/**
	 * Check if option exists
	 * @return bool
	 * @since 1.0.0
	 */
	public function option_exists(): bool {
		if ( ! WPSSPluginHelper::get_option( self::$admin_menu_perms_option ) ):
			return false;
		endif;
		
		return true;
	}
	
	/**
	 * Get admin menu data
	 * @return array
	 * @since 1.0.0
	 */
	public function get_admin_menu(): array {
		global $menu;
		if ( empty( self::$get_menus ) ):
			self::$get_menus = $menu;
		endif;
		
		return self::$get_menus;
	}
}
