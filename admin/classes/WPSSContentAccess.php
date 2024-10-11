<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSContentAccess
 * @since 1.1.0
 */
class WPSSContentAccess {
	/**
	 * @var object|null
	 * @since 1.1.0
	 */
	protected static ?object $instance = null;
	
	/**
	 * @var string
	 * @since 1.1.0
	 */
	public static string $wpss_post_type_access_key = 'wpss_content_allowed_roles';
	
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'content_access_metabox' ] );
		add_action( 'save_post', [ $this, 'content_access_save_metadata' ] );
		add_filter( 'the_content', [ $this, 'limit_role_content_access' ] );
	}
	
	/**
	 * Get class instance
	 *
	 * @return object
	 * @since 1.1.0
	 */
	public static function instance(): object {
		if ( is_null( self::$instance ) ):
			self::$instance = new self();
		endif;
		
		return self::$instance;
	}
	
	/**
	 * @param string $content
	 * @return string
	 * @since 1.1.0
	 */
	public function limit_role_content_access( string $content ): string {
		$get_allowed_roles = get_post_meta( get_the_ID(), self::$wpss_post_type_access_key, true );
		$is_allowed        = false;
		if ( $get_allowed_roles ) {
			$get_allowed_roles = json_decode( $get_allowed_roles, true );
			foreach ( $get_allowed_roles as $allowed_role ) {
				if ( current_user_can( $allowed_role ) ) {
					$is_allowed = true;
					break;
				}
			}
			
			if ( !$is_allowed ) {
				$message = WPSSPluginHelper::get_option( 'wpss_cpt_access_message' );
				if ( empty( $message ) ) {
					$message = __( 'No permission to view this content.', 'wpss-ultimate-user-management' );
				}
				$content = $message;
			}
		}
		return $content;
	}
	
	/**
	 * Define metabox
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content_access_metabox(): void {
		$get_access_cpt = WPSSPluginHelper::get_option( 'wpss_cpt_access_control' );
		if ( !empty( $get_access_cpt ) ) {
			$get_access_cpt = json_decode( $get_access_cpt, true );
		} else {
			$get_access_cpt = 'nullcpt_not_set';
		}
		add_meta_box(
			'wpss_content_access_metabox',
			__( 'Allow content only this roles: ', 'wpss-ultimate-user-management' ),
			[ $this, 'content_access_metabox_cb' ],
			$get_access_cpt,
			'side',
			'high',
		);
	}
	
	/**
	 * Render metabox content
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function content_access_metabox_cb(): void {
		$template = [ 'template' => 'content/post-type-access-metabox' ];
		WPSSAdminFrontend::render_template( $template );
	}
	
	/**
	 * Save content on post meta
	 *
	 * @param int $post_id
	 * @return void
	 * @since 1.1.0
	 */
	public function content_access_save_metadata( int $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$cpt_with_access_rules = WPSSPluginHelper::get_option( 'wpss_cpt_access_control' );
		if ( $cpt_with_access_rules ) {
			$cpt_with_access_rules = json_decode( $cpt_with_access_rules, true );
		} else {
			$cpt_with_access_rules = [];
		}
		$wpss_get_post_type = get_post_type( $post_id );
		if ( in_array( $wpss_get_post_type, $cpt_with_access_rules ) ) {
			$post_role_access = WPSSPostGet::post( self::$wpss_post_type_access_key, true ) ?? false;
			if ( $post_role_access ) {
				update_post_meta( $post_id, self::$wpss_post_type_access_key, $post_role_access );
			} else {
				delete_post_meta( $post_id, self::$wpss_post_type_access_key );
			}
		}
	}
}
