<?php
/** Prevent direct access */
if ( !function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

use WpssUserManager\Admin\WPSSAdminFrontend;
use WpssUserManager\Admin\WPSSPluginHelper;
use WpssUserManager\Admin\WPSSPostGet;
use WpssUserManager\Admin\WPSSUsers;

?>
<p>
	<?php esc_html_e( 'Select the user to add or remove access permissions. A user can own one or more permissions.', 'wpss-ultimate-user-management' ); ?>
</p>
<hr>
<?php
$search = WPSSPostGet::get( 'wpss-user-search' );
?>
<form method="get" action="<?php echo esc_url( admin_url() ); ?>" class="wpss-user-select">
    <input type="hidden" name="page" value="wpss-ultimate-user-management-admin-menu">
    <input type="hidden" name="tab" value="users-tab">
    <label for="wpss-user-search">
        <strong><?php esc_html_e( 'Search User', 'wpss-ultimate-user-management' ); ?>:</strong>
        <input type="search" value="<?php echo esc_attr( $search ); ?>" name="wpss-user-search" id="wpss-user-search" required="required">
        
        <button type="submit" class="button-primary">
			<?php esc_html_e( 'Search', 'wpss-ultimate-user-management' ); ?>
        </button>
    </label>
	<?php
	$template = [
		'template' => 'content/users-table',
		'args'     => WPSSUsers::get_users( WPSSPluginHelper::get_option( 'wpss_user_entries_screen' ), $search ),
	];
	WPSSAdminFrontend::render_template( $template );
	?>
</form><!-- .wpss-user-select -->

<div class="role-editor-messages d-none">
</div><!-- .role-editor-messages -->

<div id="user-details-container" class="user-details-container d-none">
    <hr>
    <div></div>
</div><!-- .user-details-container -->
