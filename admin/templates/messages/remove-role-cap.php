<?php
/** Prevent direct access */
if ( !function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

use WpssUserManager\Admin\WPSSRoles;

/** @var  $template */
$args    = $template['args'];
$message = sprintf(
/* Translators: 1: capability name, 2: role name */
	_n( 'Capability %1$s removed from role %2$s', 'Capabilities %1$s removed from role %2$s.', $args[0], 'wpss-ultimate-user-management' ),
	implode( ', ', $args[1] ), WPSSRoles::get_roles_names()[ $args[2] ] );
echo esc_html( $message ) . '<br>';
