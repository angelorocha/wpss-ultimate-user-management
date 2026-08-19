<?php
/**
 * Sidebar (block) widget visibility settings tab, configured per role.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSPluginHelper;
use WpssUserManager\Admin\WPSSRoles;
use WpssUserManager\Admin\WPSSWidgets;

$wpss_get_blocks    = WPSSWidgets::get_widget_blocks();
$wpss_get_widget_op = WPSSPluginHelper::get_option( 'wpss_individual_widgets' );
if ( ! empty( $wpss_get_widget_op ) ) :
	$wpss_get_widget_op = json_decode( $wpss_get_widget_op, true );
	$wpss_get_widget_op = $get_widget_op['wpss_individual_widgets'] ?? [];
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
		$wpss_list_roles = WPSSRoles::get_roles_names( false );
		unset( $wpss_list_roles['administrator'] );
		global $wp_registered_widgets;
		global $wp_registered_sidebars;
		if ( $wpss_get_blocks ) :
			foreach ( $wpss_list_roles as $wpss_role => $wpss_name ) :
				?>
				<h4><?php echo esc_attr( $wpss_name ); ?></h4>
				<div class="row block-item">
					<?php foreach ( $wpss_get_blocks as $wpss_sidebar => $wpss_block ) : ?>
						<div class="col-md-12 block-item-title">
							<?php if ( ! empty( $wp_registered_sidebars[ $wpss_sidebar ] ) ) : ?>
								<strong>
									&raquo; <?php echo esc_html( $wp_registered_sidebars[ $wpss_sidebar ]['name'] ); ?>:
								</strong>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $wpss_block ) && is_array( $wpss_block ) ) : ?>
							<?php foreach ( $wpss_block as $wpss_b ) : ?>
								<div class="col-md-3">
									<label for="wpss-block-<?php echo esc_attr( $wpss_sidebar . $wpss_role . $wpss_b ); ?>">
										<input type="checkbox"
												name="wpss_individual_widgets[<?php echo esc_attr( $wpss_role ); ?>][]"
												id="wpss-block-<?php echo esc_attr( $wpss_sidebar . $wpss_role . $wpss_b ); ?>"
												<?php if ( ! empty( $wpss_get_widget_op[ $wpss_role ] ) ) : ?>
													<?php checked( in_array( $wpss_b, $wpss_get_widget_op[ $wpss_role ], true ) ); ?>
												<?php endif; ?>
												value="<?php echo esc_attr( $wpss_b ); ?>">
										<?php
										$wpss_widget_title = WPSSWidgets::get_widget_title( $wpss_b );
										if ( empty( $wpss_widget_title ) ) :
											$wpss_widget_title = $wp_registered_widgets[ $wpss_b ]['name'] . " ({$wp_registered_widgets[ $wpss_b ]['id']})";
										endif;
										echo esc_html( $wpss_widget_title );
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
