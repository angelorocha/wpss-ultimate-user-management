<?php

namespace WpssUserManager\Admin;

/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSAdminFrontend
 * @since 1.0.0
 */
class WPSSAdminFrontend {
	
	/**
	 * Render main admin page template
	 * @return void
	 * @since 1.0.0
	 */
	public static function admin_main_content(): void {
		$args = [
			'template' => 'main',
			'args'     => '',
		];
		self::render_template( $args );
	}
	
	/**
	 * Return plugin admin menu nav
	 * @return array
	 * @since 1.0.0
	 */
	public static function nav_menu_tabs(): array {
		return [
			'roles-tab'              => __( 'Roles List', 'wpss-ultimate-user-management' ),
			'menus-tab'              => __( 'Menu Items', 'wpss-ultimate-user-management' ),
			'caps-tab'               => __( 'Capabilities List', 'wpss-ultimate-user-management' ),
			'users-tab'              => __( 'User Management', 'wpss-ultimate-user-management' ),
			'widgets-tab'            => __( 'Admin/Front Widgets', 'wpss-ultimate-user-management' ),
			'individual-widgets-tab' => __( 'Sidebar Widgets', 'wpss-ultimate-user-management' ),
			'settings-tab'           => __( 'Settings', 'wpss-ultimate-user-management' ),
		];
	}
	
	/**
	 * Get plugin admin templates
	 *
	 * @param array $template template name and args, use keys 'template' to set
	 *                        template name and ...args to pass another values
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function render_template( array $template ): void {
		if ( in_array( $template['template'], self::template_whitelist() ) ) {
			$file_path = WPSS_URCM_PLUGIN_PATH . "admin/templates/{$template['template']}.php";
			$output    = __( 'Template not found...', 'wpss-ultimate-user-management' );
			if ( file_exists( $file_path ) ):
				ob_start();
				require $file_path;
				$output = ob_get_clean();
			endif;
			
			echo wp_kses( $output, self::sanitize_output() );
		}
	}
	
	/**
	 * Sanitize html output
	 * @return array
	 * @since 1.0.0
	 */
	public static function sanitize_output(): array {
		return [
			'div'      => [ 'class' => [], 'id' => [] ],
			'table'    => [ 'class' => [], 'id' => [] ],
			'thead'    => [ 'class' => [], 'id' => [] ],
			'tr'       => [ 'class' => [], 'id' => [] ],
			'td'       => [ 'class' => [], 'id' => [], 'colspan' => [] ],
			'th'       => [ 'scope' => [] ],
			'caption'  => [ 'class' => [] ],
			'tbody'    => [ 'class' => [], 'id' => [] ],
			'tfoot'    => [ 'class' => [], 'id' => [] ],
			'a'        => [ 'href' => [], 'title' => [], 'class' => [], 'id' => [], 'target' => [] ],
			'p'        => [ 'class' => [], 'id' => [] ],
			'hr'       => [],
			'ul'       => [ 'class' => [], 'id' => [] ],
			'li'       => [ 'label' => [], 'class' => [] ],
			'h1'       => [ 'class' => [], 'id' => [] ],
			'h2'       => [ 'class' => [], 'id' => [] ],
			'h3'       => [ 'class' => [], 'id' => [] ],
			'h4'       => [ 'class' => [], 'id' => [] ],
			'u'        => [],
			'small'    => [],
			'pre'      => [],
			'br'       => [],
			'img'      => [ 'alt' => [], 'src' => [], 'class' => [], 'id' => [] ],
			'strong'   => [ 'class' => [], 'id' => [] ],
			'span'     => [
				'class'          => [],
				'id'             => [],
				'data-role-id'   => [],
				'data-role-name' => [],
				'data-user-id'   => [],
				'title'          => [],
			],
			'form'     => [ 'method' => [], 'action' => [], 'class' => [], 'id' => [] ],
			'label'    => [ 'for' => [], 'class' => [], 'id' => [] ],
			'input'    => [
				'type'        => [],
				'name'        => [],
				'value'       => [],
				'id'          => [],
				'class'       => [],
				'required'    => [],
				'checked'     => [],
				'placeholder' => [],
				'title'       => [],
			],
			'select'   => [ 'name' => [], 'class' => [], 'id' => [], 'required' => [], 'onchange' => [], ],
			'textarea' => [ 'name' => [], 'class' => [], 'id' => [], 'rows' => [], 'cols' => [] ],
			'option'   => [ 'value' => [], 'selected' => [] ],
			'button'   => [ 'type' => [], 'class' => [], 'id' => [] ],
		];
	}
	
	/**
	 * Define allowed templates
	 * @return array
	 * @since 1.0.0
	 */
	public static function template_whitelist(): array {
		return [
			'caps-tab',
			'main',
			'menus-tab',
			'roles-tab',
			'users-tab',
			'widgets-tab',
			'individual-widgets-tab',
			'settings-tab',
			'content/caps-actions',
			'content/user-details',
			'content/users-table',
			'content/post-type-access-metabox',
			'messages/user-role-add',
			'messages/user-role-remove',
			'messages/add-role-cap',
			'messages/remove-role-cap',
		];
	}
}
