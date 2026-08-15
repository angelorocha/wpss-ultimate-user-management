<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if( !defined('ABSPATH') ) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

class RoleCraftRequest {
    
    /**
     * @param string $post
     * @param bool $is_array default is false, check if value is a array
     * @param string $nonce
     * @return string
     * @since 1.0.0
     */
    public static function post(string $post, bool $is_array = false, string $nonce = ''): string {
        $output = '';
        if( empty($nonce) ) {
            return __('Request not allowed.', 'wpss-ultimate-user-management');
        }
        if( isset($_POST[$post]) && wp_verify_nonce($nonce, WPSSUserRolesCapsManager::nonce()) ) {
            if( !$is_array ) {
                $output = wp_strip_all_tags(wp_unslash($_POST[$post]));
            } else {
                $output = array_map('sanitize_text_field', wp_unslash($_POST[$post]));
                $output = wp_json_encode($output);
            }
        }
        
        return $output;
    }
    
    /**
     * @param string $get
     * @param string $nonce
     * @return string
     * @since 1.0.0
     */
    public static function get(string $get, string $nonce = ''): string {
        $output = '';
        if( empty($nonce) ) {
            return __('Request not allowed.', 'wpss-ultimate-user-management');
        }
        if( isset($_GET[$get]) && wp_verify_nonce($nonce, WPSSUserRolesCapsManager::nonce()) ) {
            $output = wp_strip_all_tags(wp_unslash($_GET[$get]));
        }
        
        return $output;
    }
}