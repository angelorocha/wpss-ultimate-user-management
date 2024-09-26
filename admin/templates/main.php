<?php

use WpssUserManager\Admin\WPSSAdminFrontend;
use WpssUserManager\Admin\WPSSPostGet;
use WpssUserManager\Admin\WPSSUserRolesCapsManager;

$menu_tab = WPSSPostGet::get('tab');
?>
<div class="wpss-roles-admin-container">
    <h3><?php esc_html_e( 'User Management', 'wpss-ultimate-user-management' ); ?></h3>
    <div class="content-nav">
        <ul class="admin-tabs">
			<?php
			$admin_menu = menu_page_url( WPSSUserRolesCapsManager::$plugin_prefix . '-admin-menu', false );
			foreach ( WPSSAdminFrontend::nav_menu_tabs() as $tab => $title ):
				$tabs_link = add_query_arg( [ 'tab' => $tab ], $admin_menu ); ?>
                <li>
                    <a href="<?php echo esc_url( $tabs_link ); ?>" title="<?php echo esc_attr( $title ); ?>">
						<?php echo esc_html( $title ); ?>
                    </a>
                </li>
			<?php endforeach; ?>
        </ul><!-- .admin-tabs-->
        
        <div class="tab-content">
			<?php if ( !empty( $menu_tab ) && in_array( $menu_tab, WPSSAdminFrontend::template_whitelist() ) ):
				WPSSAdminFrontend::render_template( [ 'template' => $menu_tab ] );
			else:
				WPSSAdminFrontend::render_template( [ 'template' => 'roles-tab' ] );
			endif; ?>
        </div><!-- .tab-content -->
    </div><!-- .content-nav -->
    
    <hr>
    
    <div class="row footer">
        <div class="col-md-6">
			<?php esc_html_e( 'Made with love', 'wpss-ultimate-user-management' ); ?> <span>&#9829;</span>
        </div>
        <div class="col-md-6 text-right">
			<?php
			$url = WPSS_URCM_PLUGIN_URI;
			esc_html_e( 'Stay in touch', 'wpss-ultimate-user-management' );
			?>
            <a href="https://br.linkedin.com/in/angelorocha" title="Follow on LinkedIn" target="_blank">
                <img src="<?php echo esc_url( $url ); ?>assets/images/linkedin.svg" alt="Follow on LinkedIn">
            </a>
            
            <a href="https://github.com/angelorocha" title="Follow on Github" target="_blank">
                <img src="<?php echo esc_url( $url ); ?>assets/images/github.svg" alt="Follow on Github">
            </a>
        </div>
    </div>
</div><!-- .wpss-roles-admin-container -->
