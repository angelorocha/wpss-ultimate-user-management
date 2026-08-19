<?php
/**
 * Admin/front widget visibility settings tab, configured per role.
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

$wpss_get_widgets   = WPSSWidgets::get_admin_widgets();
$wpss_get_widget_op = WPSSPluginHelper::get_option( 'wpss_hide_widgets' );
if ( ! empty( $wpss_get_widget_op ) ) :
	$wpss_get_widget_op = json_decode( $wpss_get_widget_op, true );
	$wpss_get_widget_op = $wpss_get_widget_op['wpss_hide_widget'] ?? [];
endif;
?>
<div class="wpss-widgets-tab">
	<p>
		<?php esc_html_e( 'Classic Widgets Permissions', 'wpss-ultimate-user-management' ); ?>
		<small>
			<?php esc_html_e( 'Use this option to disable the use of Classic Widgets for specific roles. This option removes Classic Widgets from both the administration and the frontend.', 'wpss-ultimate-user-management' ); ?>
		</small>
	</p>
	<hr>
	
	<form id="wpss-widgets-permissions" method="post" action="">
		<?php
		$wpss_list_roles = WPSSRoles::get_roles_names( false );
		unset( $wpss_list_roles['administrator'] );
		if ( $wpss_get_widgets ) :
			?>
			<?php foreach ( $wpss_list_roles as $wpss_role => $wpss_name ) : ?>
				<h4><?php echo esc_attr( $wpss_name ); ?></h4>
				<div class="row">
					<?php foreach ( $wpss_get_widgets as $wpss_key => $wpss_widget ) : ?>
						<div class="col-md-3">
							<label for="wpss_widget_<?php echo esc_attr( "$wpss_key.$wpss_role" ); ?>">
								<input type="checkbox"
										id="wpss_widget_<?php echo esc_attr( "$wpss_key.$wpss_role" ); ?>"
										name="wpss_hide_widget[<?php echo esc_attr( $wpss_role ); ?>][]"
									<?php if ( ! empty( $wpss_get_widget_op[ $wpss_role ] ) ) : ?>
										<?php checked( in_array( $wpss_key, $wpss_get_widget_op[ $wpss_role ], true ) ); ?>
									<?php endif; ?>
										value="<?php echo esc_attr( $wpss_key ); ?>">
								<?php echo esc_html( $wpss_widget['name'] ); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<div class="wpss-spacer"></div>
			<?php endforeach; ?>
		
		<?php endif; ?>
		<p class="text-center">
			<button type="submit" class="button-primary">
				<?php esc_html_e( 'Save Options', 'wpss-ultimate-user-management' ); ?>
			</button>
		</p>
		<div class="wpss-widget-messages"></div>
	</form>
</div><!-- .wpss-widgets-tab -->
