<?php
/**
 * Success message shown after adding capabilities to a role.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSRoles;

/**
 * Args passed in by WPSSAdminFrontend::render().
 *
 * @var array $template
 */
$wpss_args    = $template['args'];
$wpss_message = sprintf(
/* Translators: 1: capability name, 2: role name */
	_n( 'Capability %1$s added to role %2$s.', 'Capabilities %1$s added to role %2$s.', $wpss_args[0], 'wpss-ultimate-user-management' ),
	implode( ', ', $wpss_args[1] ),
	WPSSRoles::get_roles_names()[ $wpss_args[2] ]
);
echo esc_html( $wpss_message ) . '<br>';
