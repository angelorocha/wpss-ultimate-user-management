<?php
/**
 * Shared helper methods for reading and writing plugin options.
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
 * Class WPSSPluginHelper
 *
 * @since 1.0.0
 */
class WPSSPluginHelper {

	/**
	 * Multidimensional array search.
	 *
	 * @param string $search Value to search for.
	 * @param array  $haystack Array to search, may contain nested arrays.
	 * @param bool   $strict Whether to compare data type as well as value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function in_array_m( string $search, array $haystack, bool $strict = false ): bool {
		foreach ( $haystack as $item ) {
			// phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual -- intentional: the $strict param lets callers opt out of strict comparison.
			if ( $strict ? $item === $search : $item == $search || ( is_array( $item ) && self::in_array_m( $search, $item, $strict ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a plugin option, network-aware.
	 *
	 * @param string $option Option name.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public static function get_option( string $option ): mixed {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				return get_site_option( $option );
			} else {
				return get_blog_option( get_current_blog_id(), $option );
			}
		}

		return get_option( $option );
	}

	/**
	 * Add a plugin option, network-aware.
	 *
	 * @param string $option Option name.
	 * @param string $value Option value.
	 * @return void
	 * @since 1.0.0
	 */
	public static function add_option( string $option, string $value ): void {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				add_site_option( $option, $value );
			} else {
				add_blog_option( get_current_blog_id(), $option, $value );
			}
		} else {
			add_option( $option, $value );
		}
	}

	/**
	 * Update a plugin option, network-aware.
	 *
	 * @param string $option Option name.
	 * @param string $value New option value.
	 * @return void
	 * @since 1.0.0
	 */
	public static function update_option( string $option, string $value ): void {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				update_site_option( $option, $value );
			} else {
				update_blog_option( get_current_blog_id(), $option, $value );
			}
		} else {
			update_option( $option, $value, 'yes' );
		}
	}

	/**
	 * Delete a plugin option, network-aware.
	 *
	 * @param string $option Option name.
	 * @return void
	 * @since 1.0.0
	 */
	public static function delete_option( string $option ): void {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				delete_site_option( $option );
			} else {
				delete_blog_option( get_current_blog_id(), $option );
			}
		} else {
			delete_option( $option );
		}
	}
}
