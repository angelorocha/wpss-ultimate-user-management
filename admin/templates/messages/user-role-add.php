<?php
/** @var  $template */
$args = $template['args'];

$message = sprintf(
/* Translators: %s role name */
	_n( 'Role %s successfully added', 'Roles %s successfully added',
	    $args[0], 'wpss-ultimate-user-management' ), implode( ',', $args[1] ) );
echo esc_html( $message ) . '<br>';
