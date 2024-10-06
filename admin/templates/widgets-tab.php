<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSPluginHelper;
use WpssUserManager\Admin\WPSSRoles;
use WpssUserManager\Admin\WPSSWidgets;

$get_widgets   = WPSSWidgets::get_admin_widgets();
$get_widget_op = WPSSPluginHelper::get_option( 'wpss_hide_widgets' );
if ( !empty( $get_widget_op ) ):
	$get_widget_op = json_decode( $get_widget_op, true );
	$get_widget_op = $get_widget_op['wpss_hide_widget'] ?? [];
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
		$list_roles = WPSSRoles::get_roles_names( false );
		unset( $list_roles['administrator'] );
		if ( $get_widgets ): ?>
			<?php foreach ( $list_roles as $role => $name ): ?>
                <h4><?php echo esc_attr( $name ); ?></h4>
                <div class="row">
					<?php foreach ( $get_widgets as $key => $widget ): ?>
                        <div class="col-md-3">
                            <label for="wpss_widget_<?php echo esc_attr( "$key.$role" ); ?>">
                                <input type="checkbox"
                                       id="wpss_widget_<?php echo esc_attr( "$key.$role" ); ?>"
                                       name="wpss_hide_widget[<?php echo esc_attr( $role ); ?>][]"
									<?php if ( !empty( $get_widget_op[ $role ] ) ): ?>
										<?php checked( in_array( $key, $get_widget_op[ $role ] ) ); ?>
									<?php endif; ?>
                                       value="<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $widget['name'] ); ?>
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
