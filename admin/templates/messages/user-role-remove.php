<?php
/** Prevent direct access */
if ( !function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

/** @var  $template */
$args    = $template['args'];
$message = sprintf(
/* Translators: %s role name */
	_n( 'Role %s successfully removed', 'Roles %s successfully removed',
		$args[0], 'wpss-ultimate-user-management' ), implode( ',', $args[1] ) );
echo esc_html( $message ) . '<br>';
