<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

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
	 * @since 1.0.0
	 */
	public function set_default_plugin_options(): void {
		$options = [
			'wpss_default_role'        => 'subscriber',
			'wpss_user_entries_screen' => 10,
			'wpss_delete_plugin_data'  => 0,
			'wpss_roles_to_new_users'       => '',
		];
		$sanitize_options = array_map( 'sanitize_text_field', $options );
		foreach ( $sanitize_options as $option => $value ):
			WPSSPluginHelper::add_option( $option, $value );
		endforeach;
	}
	
	/**
	 * Plugin deactivate action
	 * @since 1.0.0
	 */
	public function remove_plugin_options(): void {
		if ( WPSSPluginHelper::get_option( 'wpss_delete_plugin_data' ) === '1' ):
			WPSSPluginHelper::delete_option( 'wpss_default_role' );
			WPSSPluginHelper::delete_option( 'wpss_user_entries_screen' );
			WPSSPluginHelper::delete_option( 'wpss_admin_menu_access' );
			WPSSPluginHelper::delete_option( 'wpss_delete_plugin_data' );
			WPSSPluginHelper::delete_option( 'wpss_roles_to_new_users' );
		endif;
	}
}
