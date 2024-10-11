<?php
/**
 *
 * @package   wpss-ultimate-user-management
 * @copyleft  2024 Angelo Rocha
 *
 * @wordpress-plugin
 * Plugin Name:       WPSS Ultimate User Management
 * Plugin URI:        https://github.com/angelorocha/wpss-ultimate-user-management
 * Description:       Manage users, roles and capabilities more easily.
 * Version:           1.1.0
 * Requires at least: 6.1
 * Requires PHP:      8.1
 * Author:            Angelo Rocha
 * Author URI:        https://angelorocha.com.br
 * License:           GNU General Public License v3 or later
 * License URI:       /LICENSE
 * Text Domain:       wpss-ultimate-user-management
 * Domain Path:       /lang
 */

namespace WpssUserManager;

use WpssUserManager\Admin\WPSSPluginInit;
use WpssUserManager\Admin\WPSSUserRolesCapsManager;
use WpssUserManager\Admin\WPSSWidgets;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/** Define constant to plugin path */
if ( !defined( 'WPSS_URCM_PLUGIN_PATH' ) ) {
	define( 'WPSS_URCM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/** Define constant to plugin url */
if ( !defined( 'WPSS_URCM_PLUGIN_URI' ) ) {
	define( 'WPSS_URCM_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}

/** Define constant to plugin main file, used in register activate/deactivate actions */
if ( !defined( 'WPSS_URCM_PLUGIN_FILE' ) ) {
	define( 'WPSS_URCM_PLUGIN_FILE', __FILE__ );
}

/** Execute autoload */
require_once WPSS_URCM_PLUGIN_PATH . 'autoload.php';
wpss_autoload( WPSS_URCM_PLUGIN_PATH );

/** Plugin init */
add_action( 'plugins_loaded', [ WPSSUserRolesCapsManager::class, 'instance' ], 0 );

/** Setup plugin */
WPSSPluginInit::setup();

/** Init widget visibility */
add_action( 'widgets_init', [ WPSSWidgets::class, 'hide_widgets' ], 11 );
