<?php

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSPluginSettings
 * @since 1.0.0
 */
class WPSSPluginSettings {
	
	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;
	
	public function __construct() {
		/** Ajax actions */
		add_action( 'wp_ajax_saveSettings', [ $this, 'saveSettings' ] );
		add_action( 'wp_ajax_nopriv_saveSettings', [ $this, 'saveSettings' ] );
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
	 * Save plugin options
	 *
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public static function saveSettings(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$get_settings_data = WPSSPostGet::post( 'settings' );
		parse_str( $get_settings_data, $settings );
		$allow_keys = [
			'wpss_default_role',
			'wpss_user_entries_screen',
			'wpss_delete_plugin_data',
			'wpss_roles_to_new_users',
			'wpss_hide_admin_bar',
		];
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				if ( in_array( $key, $allow_keys ) ) {
					if ( ! is_array( $value ) ) {
						WPSSPluginHelper::update_option( $key, sanitize_text_field( $value ) );
					} else {
						$value = array_map( 'sanitize_text_field', $value );
						WPSSPluginHelper::update_option( $key, wp_json_encode( $value ) );
					}
				}
			}
			if ( empty( $settings['wpss_roles_to_new_users'] ) ) {
				WPSSPluginHelper::update_option( 'wpss_roles_to_new_users', '' );
			}
			if ( empty( $settings['wpss_hide_admin_bar'] ) ) {
				WPSSPluginHelper::update_option( 'wpss_hide_admin_bar', '' );
			}
		}
		echo wp_json_encode( $settings );
		exit;
	}
}
