<?php
/**
 * Success message shown after adding a role to a user.
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
$args = $template['args'];

$message = sprintf(
/* Translators: %s role name */
	_n(
		'Role %s successfully added',
		'Roles %s successfully added',
		$args[0],
		'wpss-ultimate-user-management'
	),
	implode( ',', $args[1] )
);
echo esc_html( $message ) . '<br>';
