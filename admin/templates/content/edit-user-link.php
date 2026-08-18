<?php
/** Prevent direct access */
if( !defined('ABSPATH') ) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$user_object = $template['args'] ?? false;
if( $user_object ):
    echo sprintf(
        '<a href="#" title="%s" data-user-id="%s" aria-label="%s" class="wpss-open-user-role-editor">%s</a>',
        /* Translators: %s is user display name */
        esc_attr(sprintf(__('Manage roles for %s', 'wpss-ultimate-user-management'), $user_object->display_name)),
        esc_attr($user_object->ID),
        /* Translators: %s is user display name */
        esc_attr(sprintf(__('Manage roles for %s', 'wpss-ultimate-user-management'), $user_object->display_name)),
        esc_html__('Permissions', 'wpss-ultimate-user-management')
    );
endif;