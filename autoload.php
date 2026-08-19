<?php
/**
 * PSR-4 autoloader for the plugin's admin classes, read from composer.json.
 *
 * @package wpss-ultimate-user-management
 */

if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}
if ( is_array( spl_autoload_functions() ) && in_array( '__autoload', spl_autoload_functions(), true ) ) {
	spl_autoload_register( '__autoload' );
}
if ( ! function_exists( 'wpss_autoload' ) ) {
	/**
	 * Register a PSR-4 autoloader for each namespace declared in composer.json.
	 *
	 * @param string|null $dir Absolute path to the plugin directory.
	 * @return void
	 */
	function wpss_autoload( ?string $dir = null ): void {
		$dir = $dir ?? plugin_dir_path( __FILE__ );
		if ( ! file_exists( "$dir/composer.json" ) ) {
			return;
		}
		if ( file_exists( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		}
		if ( file_exists( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		}
		$content    = new WP_Filesystem_Direct( false );
		$composer   = $content->get_contents( WPSS_URCM_PLUGIN_PATH . 'composer.json' );
		$composer   = json_decode( $composer, true );
		$namespaces = $composer['autoload']['psr-4'] ?? [];
		foreach ( $namespaces as $namespace => $classpaths ) {
			if ( ! is_array( $classpaths ) ) {
				$classpaths = [ $classpaths ];
			}
			spl_autoload_register(
				function ( $classname ) use ( $namespace, $classpaths, $dir ) {
					if ( preg_match( '#^' . preg_quote( $namespace, '#' ) . '#', $classname ) ) {
							$classname = str_replace( $namespace, '', $classname );
							$filename  = preg_replace( '#\\\\#', '/', $classname ) . '.php';
						foreach ( $classpaths as $classpath ) {
							$full_path = $dir . '/' . $classpath . "/$filename";
							if ( file_exists( $full_path ) ) {
									include_once $full_path;
							}
						}
					}
				}
			);
		}
	}
}
