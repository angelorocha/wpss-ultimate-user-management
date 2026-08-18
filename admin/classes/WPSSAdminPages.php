<?php
/**
 * Controls per-role visibility of WordPress admin menu items.
 *
 * @package wpss-ultimate-user-management
 */

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSAdminPages
 *
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
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static array $get_menus = [];

	/**
	 * WordPress option to control admin menus access
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private static string $admin_menu_perms_option = 'wpss_admin_menu_access';

	/**
	 * Register the admin menu, init, and AJAX hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'get_menu_list' ] );
		add_action( 'admin_init', [ $this, 'remove_menu_items_from_role' ], 20 );

		add_action( 'wp_ajax_menage_admin_menu_options_action', [ $this, 'insert_options_action' ] );
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
	 * Insert options action
	 *
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public function insert_options_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$nonce    = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
		$get_data = RoleCraftRequest::post( 'wpss_admin_menus', false, $nonce );
		parse_str( $get_data, $menu_data );
		$key = wp_strip_all_tags( $menu_data['wpss-get-role-to-remove-menu'] );
		$val = [];
		if ( ! empty( $menu_data['wpss-show-menu-item'] ) ) {
			$val = array_map( 'wp_strip_all_tags', (array) $menu_data['wpss-show-menu-item'] );
		}
		$format_data = [ $key => $val ];
		if ( empty( $key ) ) {
			echo esc_html__( 'Select a valid role', 'wpss-ultimate-user-management' );
			exit;
		}
		self::instance()->set_option( wp_json_encode( $format_data ) );
		echo esc_html__( 'Options updated successfully', 'wpss-ultimate-user-management' );
		wp_die();
	}

	/**
	 * Remove admin menus from a role
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_menu_items_from_role(): void {
		if ( ! empty( self::get_option() ) ) {
			global $menu;
			foreach ( self::get_option() as $get_role => $get_menu ) {
				if ( current_user_can( $get_role ) && ! is_super_admin() ) {
					foreach ( $get_menu as $remove_menu ) {
						/**
						 * Keep $menu in sync with the removed item to avoid PHP warnings.
						 *
						 * @var array $menu Related bug: https://core.trac.wordpress.org/ticket/23767 -
						 *      some menus are not removed in the admin_menu hook, so this method is
						 *      linked to the admin_init hook instead.
						 */
						$menu[] = $remove_menu; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- intentional workaround for core ticket #23767, see comment above.
						remove_menu_page( $remove_menu );
					}
				}
			}
		}
	}

	/**
	 * Get filtered admin menus
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_menu_list(): array {
		/**
		 * Each entry is a raw admin menu row.
		 *
		 * @var array $menus Key 0: menu name, key 1: menu capability, key 2: menu id (used to unset).
		 */
		$menus     = self::instance()->get_admin_menu();
		$get_menus = [];
		foreach ( $menus as $menu ) {
			if ( 'read' !== $menu[1] ) {
				/**
				 * Strip menu notification badges from the option title.
				 *
				 * @var array $menu_title Regex match result.
				 */
				preg_match( '/(?<=^|>).*?(?=<|$)/s', $menu[0], $menu_title );
				$get_menus[ $menu[2] ] = esc_attr( $menu_title[0] );
			}
		}

		return $get_menus;
	}

	/**
	 * Get option value
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_option(): array {
		$instance = self::instance();
		$output   = [];
		if ( $instance->option_exists() ) {
			$output = json_decode( WPSSPluginHelper::get_option( self::$admin_menu_perms_option ), true );
		}

		return $output;
	}

	/**
	 * Set access options.
	 *
	 * @param string $value JSON-encoded map of role => hidden menu ids.
	 * @return void
	 * @since 1.0.0
	 */
	public function set_option( string $value ): void {
		if ( ! self::option_exists() ) {
			WPSSPluginHelper::add_option( self::$admin_menu_perms_option, $value );
		} else {
			self::update_option( $value );
		}
	}

	/**
	 * Update access options.
	 *
	 * @param string $update JSON-encoded map of role => hidden menu ids to merge in.
	 * @return void
	 * @since 1.0.0
	 */
	public function update_option( string $update ): void {
		$update_data = self::get_option();
		$get_data    = json_decode( $update, true );
		foreach ( $get_data as $key => $val ) {
			$update_data[ $key ] = $val;
			if ( empty( $update_data[ $key ] ) ) {
				unset( $update_data[ $key ] );
			}
		}
		WPSSPluginHelper::update_option( self::$admin_menu_perms_option, wp_json_encode( $update_data ) );
	}

	/**
	 * Check if option exists
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function option_exists(): bool {
		if ( ! WPSSPluginHelper::get_option( self::$admin_menu_perms_option ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get admin menu data
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_admin_menu(): array {
		global $menu;
		if ( empty( self::$get_menus ) ) {
			self::$get_menus = $menu;
		}

		return self::$get_menus;
	}
}
