<?php

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSCaps
 * @since 1.0.0
 */
class WPSSCaps {
	
	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.0.0
	 */
	protected static ?object $instance = null;
	
	/**
	 * Excluded post types capabilities
	 * @since 1.0.0
	 * @var array|string[]
	 */
	private static array $post_type_filter = [
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'wp_template',
	];
	
	/**
	 * Excluded taxonomies capabilities
	 * @since 1.0.0
	 * @var array
	 */
	private static array $taxonomy_filter = [ 'nav_menu', 'link_category', 'wp_theme' ];
	
	public function __construct() {
		/** Ajax call to get role capabilities */
		add_action( 'wp_ajax_wpss_get_role_capabilities_action', [ $this, 'get_role_capabilities_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_get_role_capabilities_action', [ $this, 'get_role_capabilities_action' ] );
		
		/** Ajax call to set capabilities to a role */
		add_action( 'wp_ajax_wpss_set_capabilities_to_role_action', [ $this, 'set_capabilities_to_role_action' ] );
		add_action( 'wp_ajax_nopriv_wpss_set_capabilities_to_role_action', [
			$this,
			'set_capabilities_to_role_action',
		] );
	}
	
	/**
	 * Get class instance
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public static function instance(): object {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Set capabilities to role
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public static function set_capabilities_to_role_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$get_data_caps = WPSSPostGet::post( 'capabilities' );
		parse_str( $get_data_caps, $get_caps );
		$role = WPSSPostGet::post( 'role' );
		if ( $role ) {
			$instance     = self::instance();
			$capabilities = !empty( $get_caps ) ? array_unique( $get_caps['wpss-caps-to-role'] ) : [];
			$capabilities = array_map( fn( $sanitize ) => sanitize_text_field( $sanitize ), $capabilities );
			/** Insert role capabilities */
			$instance->set_role_caps( $role, $capabilities );
			/** Remove caps from role */
			$instance->remove_role_caps( $role, $capabilities );
		}
		exit;
	}
	
	/**
	 * Get capabilities form from role
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public static function get_role_capabilities_action(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$role_caps = WPSSPostGet::post( 'role_caps' );
		if ( !empty( $role_caps ) ) {
			$template = [
				'template' => 'content/caps-actions',
				'args'     => $role_caps,
			];
			WPSSAdminFrontend::render_template( $template );
		}
		exit;
	}
	
	/**
	 * Get all available WordPress Capabilities
	 *
	 * @param string $type choose the type of capability to retrieve, accepted values:
	 *                     admin: retrieve admin capabilities
	 *                     tax: retrieve taxonomy capabilities
	 *                     post_type: retrieve post type capability
	 *                     all: retrieve all WordPress capabilities
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public static function get_caps( string $type ): ?array {
		$instance = self::instance();
		
		return match ( $type ) {
			'admin' => $instance->admin_capabilities(),
			'tax' => $instance->get_taxonomies_capabilities_list(),
			'post_type' => $instance->get_post_type_capabilities_list(),
			'all' => array_merge( $instance->admin_capabilities(), $instance->capabilities() ),
			default => null,
		};
	}
	
	/**
	 * Get capabilities from a role
	 *
	 * @param string $role
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_cap_by_role( string $role ): array {
		return array_keys( get_role( $role )->capabilities );
	}
	
	/**
	 * Insert capabilities to a role
	 *
	 * @param string $role
	 * @param array $capabilities
	 *
	 * @since 1.0.0
	 */
	private function set_role_caps( string $role, array $capabilities ): void {
		$output = [];
		if ( !empty( $capabilities ) ) {
			foreach ( $capabilities as $cap ) {
				if ( !get_role( $role )->has_cap( $cap ) ) {
					get_role( $role )->add_cap( sanitize_text_field( $cap ) );
					$output[] = $cap;
				}
			}
		}
		if ( !empty( $output ) ) {
			$count = count( $output );
			WPSSAdminFrontend::render_template(
				[
					'template' => 'messages/add-role-cap',
					'args'     => [ $count, $output, $role ],
				]
			);
		}
	}
	
	/**
	 * Remove capabilities from a role
	 *
	 * @param string $role
	 * @param array $capabilities
	 * @return void
	 * @since 1.0.0
	 */
	private function remove_role_caps( string $role, array $capabilities ): void {
		$remove_caps = array_diff( self::get_cap_by_role( $role ), $capabilities );
		$output      = [];
		if ( $remove_caps ) {
			foreach ( $remove_caps as $remove ) {
				if ( get_role( $role )->has_cap( $remove ) && 'read' !== $remove ) {
					get_role( $role )->remove_cap( sanitize_text_field( $remove ) );
					$output[] = $remove;
				}
			}
		}
		if ( !empty( $output ) ) {
			$count = count( $output );
			WPSSAdminFrontend::render_template(
				[
					'template' => 'messages/remove-role-cap',
					'args'     => [ $count, $output, $role ],
				]
			);
		}
	}
	
	/**
	 * Retrieve admin capabilities, hide post type and taxonomy capabilities
	 * @return array
	 * @since 1.0.0
	 */
	private function admin_capabilities(): array {
		$admin_caps = [];
		foreach ( self::get_cap_by_role( 'administrator' ) as $key ) {
			if ( !WPSSPluginHelper::in_array_m( $key, self::capabilities() ) && !str_contains( $key, 'level_' ) ) {
				$admin_caps[] = $key;
			}
		}
		
		/** Avoid errors if this capability are removed */
		if ( in_array( 'read', $admin_caps ) ) {
			unset( $admin_caps[ array_search( 'read', $admin_caps ) ] );
		}
		
		return $admin_caps;
	}
	
	/**
	 * Get all post type and taxonomy capabilities
	 * @return array
	 * @since 1.0.0
	 */
	private function capabilities(): array {
		return array_merge( self::get_post_type_capabilities_list(), self::get_taxonomies_capabilities_list() );
	}
	
	/**
	 * Get post types capabilities list
	 * @return array
	 * @since 1.0.0
	 */
	private function get_post_type_capabilities_list(): array {
		$post_types = [];
		foreach ( get_post_types() as $post_type ) {
			if ( !in_array( $post_type, self::$post_type_filter ) ) {
				$post_types[ get_post_type_object( $post_type )->name ] = json_decode( wp_json_encode( get_post_type_object( $post_type )->cap ), true );
				/** Avoid errors if this capability are removed */
				if ( in_array( 'read', $post_types[ get_post_type_object( $post_type )->name ] ) ) {
					unset( $post_types[ get_post_type_object( $post_type )->name ]['read'] );
				}
			}
		}
		
		return $post_types;
	}
	
	/**
	 * Get taxonomies capabilities list
	 * @return array
	 * @since 1.0.0
	 */
	private function get_taxonomies_capabilities_list(): array {
		$tax = [];
		foreach ( get_taxonomies() as $taxonomy ) {
			if ( !in_array( $taxonomy, self::$taxonomy_filter ) ) {
				$tax[ get_taxonomy( $taxonomy )->name ] = json_decode( wp_json_encode( get_taxonomy( $taxonomy )->cap ), true );
				/** Remove redundant 'edit_posts' capability from taxonomies */
				if ( in_array( 'edit_posts', $tax[ get_taxonomy( $taxonomy )->name ] ) ) {
					$remove = array_search( 'edit_posts', $tax[ get_taxonomy( $taxonomy )->name ] );
					unset( $tax[ get_taxonomy( $taxonomy )->name ][ $remove ] );
				}
			}
		}
		
		return $tax;
	}
}
