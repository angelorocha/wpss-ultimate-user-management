<?php

namespace WpssUserManager\Admin;

use JetBrains\PhpStorm\NoReturn;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class WPSSWidgets {
	
	/**
	 * @var object|null
	 * @since 1.0.0
	 */
	private static ?object $instance = null;
	
	public function __construct() {
		/** Ajax actions */
		add_action( 'wp_ajax_save_widget_options', [ $this, 'save_widget_options' ] );
		add_action( 'wp_ajax_nopriv_save_widget_options', [ $this, 'save_widget_options' ] );
		add_action( 'wp_ajax_save_individual_widgets_permissions', [ $this, 'save_individual_widgets_permissions' ] );
		add_action( 'wp_ajax_nopriv_save_individual_widgets_permissions', [ $this, 'save_individual_widgets_permissions' ] );
		
		/** Check sidebar widget */
		add_filter( 'widget_display_callback', [ $this, 'hide_individual_widgets' ], 10, 3 );
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
	 * Save widget options
	 *
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public static function save_widget_options(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$widget_data = WPSSPostGet::post( 'wpss_widgets' );
		parse_str( $widget_data, $widgets_options );
		if ( isset( $widgets_options['wpss_hide_widget']['administrator'] ) ) {
			unset( $widgets_options['wpss_hide_widget']['administrator'] );
		}
		WPSSPluginHelper::update_option( 'wpss_hide_widgets', wp_strip_all_tags( wp_json_encode( $widgets_options ) ) );
		echo esc_html__( 'Options saved!', 'wpss-ultimate-user-management' );
		exit;
	}
	
	/**
	 * Save individual widgets permissions
	 *
	 * @return void
	 * @since 1.0.0
	 */
	#[NoReturn] public static function save_individual_widgets_permissions(): void {
		WPSSUserRolesCapsManager::wpss_ajax_check_referer();
		$widget_data = WPSSPostGet::post( 'individual_widgets' );
		parse_str( $widget_data, $widgets_options );
		if ( isset( $widgets_options['wpss_individual_widgets']['administrator'] ) ) {
			unset( $widgets_options['wpss_individual_widgets']['administrator'] );
		}
		WPSSPluginHelper::update_option( 'wpss_individual_widgets', wp_strip_all_tags( wp_json_encode( $widgets_options ) ) );
		echo esc_html__( 'Options saved!', 'wpss-ultimate-user-management' );
		exit;
	}
	
	/**
	 * Check if exists any rule to show/hide widgets on frontend to specific hole
	 *
	 * @param $instance
	 * @param $widget
	 * @param $args
	 * @return array|bool
	 * @since 1.0.0
	 */
	public function hide_individual_widgets( $instance, $widget, $args ): array|bool {
		$hide_widgets = WPSSPluginHelper::get_option( 'wpss_individual_widgets' );
		if ( $hide_widgets ) {
			$hide_widgets = json_decode( $hide_widgets, true );
			$hide_widgets = $hide_widgets['wpss_individual_widgets'] ?? [];
			foreach ( $hide_widgets as $key => $value ) {
				if ( current_user_can( $key ) && in_array( $widget->id, $value ) ) {
					return false;
				}
			}
		}
		return $instance;
	}
	
	/**
	 * Hide widgets for specific roles
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function hide_widgets(): void {
		$hidden_widgets = WPSSPluginHelper::get_option( 'wpss_hide_widgets' );
		if ( $hidden_widgets ) {
			$hidden_widgets = json_decode( $hidden_widgets, true );
		}
		if ( !WPSSUserRolesCapsManager::is_plugin_menu_page() ) {
			if ( !empty( $hidden_widgets ) ) {
				foreach ( $hidden_widgets as $widget ) {
					foreach ( $widget as $role => $hide ) {
						if ( current_user_can( $role ) ) {
							foreach ( $hide as $w ) {
								unregister_widget( wp_strip_all_tags( $w ) );
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * Retrieve all active sidebar widgets
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_widget_blocks(): array {
		$get_blocks = wp_get_sidebars_widgets();
		unset( $get_blocks['wp_inactive_widgets'] );
		$active_blocks = [];
		foreach ( $get_blocks as $key => $block ) {
			if ( !empty( $block ) ) {
				$active_blocks[ $key ] = $block;
			}
		}
		
		return $active_blocks;
	}
	
	/**
	 * Get widget title by id
	 * @param $widget_id
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_widget_title( $widget_id ): string {
		global $wp_registered_widgets;
		if ( !isset( $wp_registered_widgets[ $widget_id ] ) ) {
			return '';
		}
		$widget_obj       = $wp_registered_widgets[ $widget_id ];
		$widget_num       = preg_replace( '/[^0-9]/', '', $widget_id );
		$widget_base      = $widget_obj['callback'][0]->id_base;
		$widget_instances = WPSSPluginHelper::get_option( 'widget_' . $widget_base );
		if ( isset( $widget_instances[ $widget_num ] ) ) {
			$widget_instance = $widget_instances[ $widget_num ];
			return !empty( $widget_instance['title'] ) ? $widget_instance['title'] : '';
		}
		
		return '';
	}
	
	/**
	 * Get all registered widgets
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_admin_widgets(): array {
		$widgets = [];
		if ( property_exists( 'WP_Widget_Factory', 'widgets' ) ) {
			$get_widgets = $GLOBALS['wp_widget_factory']->widgets;
			if ( $get_widgets ) {
				foreach ( $get_widgets as $key => $widget ) {
					$widgets[ $key ] = [
						'name'    => $widget->name,
						'id'      => $widget->id,
						'id_base' => $widget->control_options['id_base'],
					];
				}
			}
		}
		return $widgets;
	}
}
