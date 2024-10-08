<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSPluginInit
 * @since 1.0.0
 */
class WPSSPluginInit {
	
	/**
	 * Initial plugin configuration
	 * @return void
	 * @since 1.0.0
	 */
	public static function setup(): void {
		$instance = new self();
		/** Plugin activate hook */
		register_activation_hook( WPSS_URCM_PLUGIN_FILE, [ $instance, 'set_default_plugin_options' ] );
		/** Plugin deactivate hook */
		register_deactivation_hook( WPSS_URCM_PLUGIN_FILE, [ $instance, 'remove_plugin_options' ] );
	}
	
	/**
	 * Plugin activate action
	 * @return void
	 * @since 1.0.0
	 */
	public function set_default_plugin_options(): void {
		$options          = [
			'wpss_default_role'        => 'subscriber',
			'wpss_user_entries_screen' => 10,
			'wpss_delete_plugin_data'  => 0,
			'wpss_roles_to_new_users'  => '',
			'wpss_hide_admin_bar'      => '',
			'wpss_hide_widgets'        => '',
			'wpss_individual_widgets'  => '',
		];
		$sanitize_options = array_map( 'sanitize_text_field', $options );
		foreach ( $sanitize_options as $option => $value ) {
			WPSSPluginHelper::add_option( $option, $value );
		}
	}
	
	/**
	 * Plugin deactivate action
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_plugin_options(): void {
		if ( WPSSPluginHelper::get_option( 'wpss_delete_plugin_data' ) === '1' ) {
			WPSSPluginHelper::delete_option( 'wpss_default_role' );
			WPSSPluginHelper::delete_option( 'wpss_user_entries_screen' );
			WPSSPluginHelper::delete_option( 'wpss_admin_menu_access' );
			WPSSPluginHelper::delete_option( 'wpss_delete_plugin_data' );
			WPSSPluginHelper::delete_option( 'wpss_roles_to_new_users' );
			WPSSPluginHelper::delete_option( 'wpss_hide_admin_bar' );
			WPSSPluginHelper::delete_option( 'wpss_hide_widgets' );
			WPSSPluginHelper::delete_option( 'wpss_individual_widgets' );
		}
	}
}
