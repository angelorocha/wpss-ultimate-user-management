<?php
/**
 *
 * @package   wpss-ultimate-user-management
 * @copyleft  2022 Angelo Rocha
 *
 * @wordpress-plugin
 * Plugin Name:       WPSS Ultimate User Management
 * Plugin URI:        https://github.com/angelorocha/wpss-ultimate-user-management
 * Description:       Manage users, roles and capabilities more easily.
 * Version:           1.0.0
 * Requires at least: 6
 * Requires PHP:      8
 * Author:            Angelo Rocha
 * Author URI:        https://angelorocha.com.br
 * License:           GNU General Public License v3 or later
 * License URI:       /LICENSE
 * Text Domain:       wpss-ultimate-user-management
 * Domain Path:       /lang
 * GitHub Plugin URI: ''
 */

namespace WpssUserManager;

use WpssUserManager\Admin\WPSSPluginInit;
use WpssUserManager\Admin\WPSSUserRolesCapsManager;

/** Prevent direct access */
if ( ! function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

/** Define constant to plugin path */
if ( ! defined( 'WPSS_URCM_PLUGIN_PATH' ) ):
	define( 'WPSS_URCM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
endif;

/** Define constant to plugin url */
if ( ! defined( 'WPSS_URCM_PLUGIN_URI' ) ):
	define( 'WPSS_URCM_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
endif;

/** Define constant to plugin main file, used in register activate/deactivate actions */
if ( ! defined( 'WPSS_URCM_PLUGIN_FILE' ) ):
	define( 'WPSS_URCM_PLUGIN_FILE', __FILE__ );
endif;

/** Execute autoload */
require_once WPSS_URCM_PLUGIN_PATH . 'autoload.php';
wpss_autoload( WPSS_URCM_PLUGIN_PATH );

/** Plugin init */
add_action( 'plugins_loaded', [ WPSSUserRolesCapsManager::class, 'instance' ], 0 );

/** Setup plugin */
WPSSPluginInit::setup();
