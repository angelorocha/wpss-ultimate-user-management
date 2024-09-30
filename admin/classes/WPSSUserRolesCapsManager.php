<?php

namespace WpssUserManager\Admin;

/**
 * Class WPSSRoleEditor
 *
 * @since 1.0.0
 */
class WPSSUserRolesCapsManager {
	
	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;
	
	/**
	 * Define plugin prefix
	 * @var string
	 * @since 1.0.0
	 */
	public static string $plugin_prefix = 'wpss-ultimate-user-management';
	
	/**
	 * Prefix plugin
	 * @since 1.0.0
	 * @var array
	 */
	private array $global_params = [];
	
	/**
	 * Secure nonce for ajax calls
	 * @since 1.0.0
	 * @var string
	 */
	private static string $nonce = 'wpss-ajax-security-nonce';
	
	/**
	 * Define plugin assets version
	 * @since 1.0.0
	 * @var int
	 */
	private static int $plugin_file_version = 20240926;
	
	/**
	 * Initialize the plugin
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( empty( $this->global_params ) && is_admin() ):
			$this->global_params = [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::$nonce ),
			];
		endif;
		
		/** Setup plugin admin menu page */
		add_action( 'admin_menu', [ $this, 'wpss_user_admin_menu' ] );
		/** Load plugin text domain */
		add_action( 'plugins_loaded', [ $this, 'wpss_load_plugin_text_domain' ] );
		/** Load plugin admin scripts */
		add_action( 'admin_enqueue_scripts', [ $this, 'wpss_scripts_styles' ] );
		/** Hide admin bar */
		add_action( 'after_setup_theme', [ $this, 'wpss_hide_admin_bar' ] );
		/** Load plugin deps */
		add_action( 'init', [ WPSSAdminPages::class, 'instance' ] );
		add_action( 'init', [ WPSSCaps::class, 'instance' ] );
		add_action( 'init', [ WPSSUsers::class, 'instance' ] );
		add_action( 'init', [ WPSSRoles::class, 'instance' ] );
		add_action( 'init', [ WPSSPluginSettings::class, 'instance' ] );
		add_action( 'init', [ WPSSWidgets::class, 'instance' ] );
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
	 * Validate ajax calls
	 * @since 1.0.0
	 */
	public static function wpss_ajax_check_referer(): void {
		if ( !current_user_can( 'administrator' ) ):
			header( 'HTTP/1.0 403 Forbidden' );
			exit;
		endif;
		check_ajax_referer( self::$nonce, 'nonce' );
	}
	
	/**
	 * Hide admin bar to specific roles
	 * @return void
	 * @since 1.0.0
	 */
	public function wpss_hide_admin_bar(): void {
		$roles_to_hide = WPSSPluginHelper::get_option( 'wpss_hide_admin_bar' );
		if ( !empty( $roles_to_hide ) ) {
			$roles_to_hide = json_decode( $roles_to_hide, true );
			$roles_to_hide = is_array( $roles_to_hide ) ? $roles_to_hide : [];
			foreach ( $roles_to_hide as $hide ) {
				if ( current_user_can( $hide ) ) {
					show_admin_bar( false );
				}
			}
		}
	}
	
	/**
	 * Setup plugin admin page
	 * @return void
	 * @since 1.0.0
	 */
	public function wpss_user_admin_menu(): void {
		add_menu_page(
			__( 'User Management', 'wpss-ultimate-user-management' ),
			__( 'User Management', 'wpss-ultimate-user-management' ),
			'administrator',
			self::$plugin_prefix . '-admin-menu',
			[ WPSSAdminFrontend::class, 'admin_main_content' ],
			'dashicons-privacy',
			5
		);
	}
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wpss_load_plugin_text_domain(): void {
		load_plugin_textdomain( self::$plugin_prefix, false, WPSS_URCM_PLUGIN_PATH . '/lang/' );
	}
	
	/**
	 * Load admin styles and scripts
	 * @since 1.0.0
	 */
	public function wpss_scripts_styles(): void {
		if ( self::is_plugin_menu_page() ) {
			wp_enqueue_style( self::$plugin_prefix . '-css', WPSS_URCM_PLUGIN_URI . 'assets/css/main.min.css', [], self::$plugin_file_version );
			wp_enqueue_script( self::$plugin_prefix . '-js', WPSS_URCM_PLUGIN_URI . 'assets/js/js.min.js', [ 'jquery' ], self::$plugin_file_version, true );
			wp_localize_script( self::$plugin_prefix . '-js', 'wpss_user_management_object', $this->global_params );
		}
	}
	
	/**
	 * @return bool
	 * @since 1.0.0
	 */
	public static function is_plugin_menu_page(): bool {
		$wpss_menu = WPSSPostGet::get( 'page' );
		if ( !empty( $wpss_menu ) && $wpss_menu === self::$plugin_prefix . '-admin-menu' ) {
			return true;
		}
		return false;
	}
}