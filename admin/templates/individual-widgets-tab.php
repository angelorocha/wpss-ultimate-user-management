<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSPluginHelper;
use WpssUserManager\Admin\WPSSRoles;
use WpssUserManager\Admin\WPSSWidgets;

$get_blocks    = WPSSWidgets::get_widget_blocks();
$get_widget_op = WPSSPluginHelper::get_option( 'wpss_individual_widgets' );
if ( !empty( $get_widget_op ) ):
	$get_widget_op = json_decode( $get_widget_op, true );
	$get_widget_op = $get_widget_op['wpss_individual_widgets'] ?? [];
endif;
?>
<div class="wpss-widgets-tab">
    <p>
		<?php esc_html_e( 'Sidebar Widgets', 'wpss-ultimate-user-management' ); ?>
        <small>
			<?php esc_html_e( 'Use this option to show/hide individual Widgets/Blocks in any sidebar. This option hides Widgets only from the frontend.', 'wpss-ultimate-user-management' ); ?>
        </small>
    </p>
    <hr>
    
    <form id="wpss-individual-widgets-permissions" method="post" action="">
		<?php
		$list_roles = WPSSRoles::get_roles_names( false );
		unset( $list_roles['administrator'] );
		global $wp_registered_widgets;
		global $wp_registered_sidebars;
		if ( $get_blocks ):
			foreach ( $list_roles as $role => $name ): ?>
                <h4><?php echo esc_attr( $name ); ?></h4>
                <div class="row block-item">
					<?php foreach ( $get_blocks as $sidebar => $block ): ?>
                        <div class="col-md-12 block-item-title">
                            <strong>
                                &raquo; <?php echo esc_html( $wp_registered_sidebars[ $sidebar ]['name'] ); ?>:
                            </strong>
                        </div>
						<?php if ( !empty( $block ) ): ?>
							<?php foreach ( $block as $b ): ?>
                                <div class="col-md-3">
                                    <label for="wpss-block-<?php echo esc_attr( $sidebar . $role . $b ); ?>">
                                        <input type="checkbox"
                                               name="wpss_individual_widgets[<?php echo esc_attr( $role ); ?>][]"
                                               id="wpss-block-<?php echo esc_attr( $sidebar . $role . $b ); ?>"
											<?php if ( !empty( $get_widget_op[ $role ] ) ): ?>
												<?php checked( in_array( $b, $get_widget_op[ $role ] ) ); ?>
											<?php endif; ?>
                                               value="<?php echo esc_attr( $b ); ?>">
										<?php
										$widget_title = WPSSWidgets::get_widget_title( $b );
										if ( empty( $widget_title ) ):
											$widget_title = $wp_registered_widgets[ $b ]['name'] . " ({$wp_registered_widgets[ $b ]['id']})";
										endif;
										echo esc_html( $widget_title );
										?>
                                    </label>
                                </div>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
                </div>
                <hr>
			<?php endforeach; ?>
            <p class="text-center">
                <button type="submit" class="button-primary">
					<?php esc_html_e( 'Save Options', 'wpss-ultimate-user-management' ); ?>
                </button>
            </p>
		<?php endif; ?>
        <div class="wpss-widget-messages"></div>
    </form>
</div><!-- .wpss-widgets-tab -->