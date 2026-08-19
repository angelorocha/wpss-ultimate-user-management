<?php
/**
 * Success message shown after removing a role from a user.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Args passed in by WPSSAdminFrontend::render().
 *
 * @var array $template
 */
$wpss_args    = $template['args'];
$wpss_message = sprintf(
/* Translators: %s role name */
	_n(
		'Role %s successfully removed',
		'Roles %s successfully removed',
		$wpss_args[0],
		'wpss-ultimate-user-management'
	),
	implode( ',', $wpss_args[1] )
);
echo esc_html( $wpss_message ) . '<br>';
