<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class WPSSPostGet {
	
	/**
	 * Define post and get secure nonce for requests
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $post_nonce = 'wpss_post_nonce';
	
	/**
	 * @param string $post
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function post( string $post ): string {
		$output = '';
		$nonce = wp_create_nonce( self::$post_nonce );
		if ( isset( $_POST[ $post ] ) && wp_verify_nonce( $nonce, self::$post_nonce ) ) {
			$output = wp_strip_all_tags( wp_unslash( $_POST[ $post ] ) );
		}
		
		return $output;
	}
	
	/**
	 * @param string $get
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get( string $get ): string {
		$output = '';
		$nonce = wp_create_nonce( self::$post_nonce );
		if ( isset( $_GET[ $get ] ) && wp_verify_nonce( $nonce, self::$post_nonce ) ) {
			$output = wp_strip_all_tags( wp_unslash( $_GET[ $get ] ) );
		}
		
		return $output;
	}
}