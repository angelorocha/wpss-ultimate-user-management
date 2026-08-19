<?php
/**
 * Plugin admin page shell: renders the tab navigation and the active tab.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSAdminFrontend;
use WpssUserManager\Admin\RoleCraftRequest;
use WpssUserManager\Admin\WPSSUserRolesCapsManager;

$wpss_nonce    = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
$wpss_menu_tab = RoleCraftRequest::get( 'tab', $wpss_nonce );
?>
<div class="wpss-roles-admin-container">
	<h3>
		<?php
		esc_html_e( 'User Management', 'wpss-ultimate-user-management' );

		$wpss_section_title = WPSSAdminFrontend::nav_menu_tabs()['roles-tab'];
		if ( ! empty( $wpss_menu_tab ) ) :
			$wpss_section_title = WPSSAdminFrontend::nav_menu_tabs()[ $wpss_menu_tab ];
		endif;
		echo esc_html( " - $wpss_section_title" );
		?>
	</h3>
	<div class="content-nav">
		<ul class="admin-tabs">
			<?php
			$wpss_admin_menu  = menu_page_url( WPSSUserRolesCapsManager::$plugin_prefix . '-admin-menu', false );
			$wpss_current_tab = 0;
			foreach ( WPSSAdminFrontend::nav_menu_tabs() as $wpss_tab => $wpss_title ) :
				++$wpss_current_tab;
				$wpss_active_tab = 1 === $wpss_current_tab && empty( $wpss_menu_tab ) ? 'active' : '';
				if ( ! empty( $wpss_menu_tab ) && $wpss_tab === $wpss_menu_tab ) :
					$wpss_active_tab = 'active';
				endif;
				$wpss_tabs_link = add_query_arg( [ 'tab' => $wpss_tab ], $wpss_admin_menu );
				?>
				<li class="<?php echo esc_attr( $wpss_active_tab ); ?>">
					<a href="<?php echo esc_url( $wpss_tabs_link ); ?>" title="<?php echo esc_attr( $wpss_title ); ?>">
						<?php echo esc_html( $wpss_title ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul><!-- .admin-tabs-->
		
		<div class="tab-content">
			<?php
			if ( ! empty( $wpss_menu_tab ) && in_array( $wpss_menu_tab, WPSSAdminFrontend::template_whitelist(), true ) ) :
				WPSSAdminFrontend::render( [ 'template' => $wpss_menu_tab ] );
			else :
				WPSSAdminFrontend::render( [ 'template' => 'roles-tab' ] );
			endif;
			?>
		</div><!-- .tab-content -->
	</div><!-- .content-nav -->
	
	<hr>
	
	<div class="row footer">
		<div class="col-md-6">
			<?php esc_html_e( 'Made with love', 'wpss-ultimate-user-management' ); ?> <span>&#9829;</span>
		</div>
		<div class="col-md-6 text-right">
			<?php
			$wpss_url = WPSS_URCM_PLUGIN_URI;
			esc_html_e( 'Stay in touch', 'wpss-ultimate-user-management' );
			?>
			<a href="https://br.linkedin.com/in/angelorocha" title="<?php esc_attr_e( 'Follow on LinkedIn', 'wpss-ultimate-user-management' ); ?>" target="_blank">
				<img src="<?php echo esc_url( "{$wpss_url}assets/images/linkedin.svg" ); ?>" alt="<?php esc_attr_e( 'Follow on LinkedIn', 'wpss-ultimate-user-management' ); ?>">
			</a>
			
			<a href="https://github.com/angelorocha" title="<?php esc_attr_e( 'Follow on Github', 'wpss-ultimate-user-management' ); ?>" target="_blank">
				<img src="<?php echo esc_url( "{$wpss_url}assets/images/github.svg" ); ?>" alt="<?php esc_attr_e( 'Follow on Github', 'wpss-ultimate-user-management' ); ?>">
			</a>
		</div>
	</div>
</div><!-- .wpss-roles-admin-container -->
