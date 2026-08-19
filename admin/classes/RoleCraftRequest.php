<?php
/**
 * Sanitizes and reads $_POST/$_GET request values used by the plugin's AJAX handlers.
 *
 * @package wpss-ultimate-user-management
 */

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Reads and sanitizes $_POST/$_GET values, gated by a nonce.
 *
 * @since 1.0.0
 */
class RoleCraftRequest {

	/**
	 * Read and sanitize a $_POST value.
	 *
	 * @param string $post Name of the $_POST key to read.
	 * @param bool   $is_array Default is false, check if value is an array.
	 * @param string $nonce Nonce to verify before reading the value.
	 * @return string|null
	 * @since 1.0.0
	 */
	public static function post( string $post, bool $is_array = false, string $nonce = '' ): string|null {
		$output = null;
		if ( empty( $nonce ) ) {
			return __( 'Request not allowed.', 'wpss-ultimate-user-management' );
		}
		if ( isset( $_POST[ $post ] ) && wp_verify_nonce( $nonce, WPSSUserRolesCapsManager::nonce() ) ) {
			if ( ! $is_array ) {
				$output = wp_strip_all_tags( wp_unslash( $_POST[ $post ] ) );
			} else {
				$output = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $post ] ) );
				$output = wp_json_encode( $output );
			}
		}

		return $output;
	}

	/**
	 * Read and sanitize a $_GET value.
	 *
	 * @param string $get Name of the $_GET key to read.
	 * @param string $nonce Nonce to verify before reading the value.
	 * @return string|null
	 * @since 1.0.0
	 */
	public static function get( string $get, string $nonce = '' ): string|null {
		$output = '';
		if ( empty( $nonce ) ) {
			return __( 'Request not allowed.', 'wpss-ultimate-user-management' );
		}
		if ( isset( $_GET[ $get ] ) && wp_verify_nonce( $nonce, WPSSUserRolesCapsManager::nonce() ) ) {
			$output = wp_strip_all_tags( wp_unslash( $_GET[ $get ] ) );
		}

		return $output;
	}
}
