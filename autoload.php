<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Você não tem permissão para acessar esta página diretamente.' );
}
if (
	is_array( spl_autoload_functions() )
	&& in_array( '__autoload', spl_autoload_functions() )
) {
	spl_autoload_register( '__autoload' );
}
if ( ! function_exists( 'wpss_autoload' ) ) {
	function wpss_autoload( $dir ): void {
		if ( ! file_exists( "$dir/composer.json" ) ) {
			return;
		}
		$composer = wp_remote_get( WPSS_URCM_PLUGIN_URI . "composer.json" );
		$composer = wp_remote_retrieve_body( $composer );
		$composer = json_decode( $composer, true );
		$namespaces = $composer['autoload']['psr-4'] ?? [];
		foreach ( $namespaces as $namespace => $classpaths ) {
			if ( ! is_array( $classpaths ) ) {
				$classpaths = [ $classpaths ];
			}
			spl_autoload_register( function( $classname ) use ( $namespace, $classpaths, $dir ) {
				if ( preg_match( "#^" . preg_quote( $namespace ) . "#", $classname ) ) {
					$classname = str_replace( $namespace, "", $classname );
					$filename = preg_replace( "#\\\\#", "/", $classname ) . ".php";
					foreach ( $classpaths as $classpath ) {
						$full_path = $dir . "/" . $classpath . "/$filename";
						if ( file_exists( $full_path ) ) {
							include_once $full_path;
						}
					}
				}
			} );
		}
	}
}
