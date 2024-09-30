<?php
/** Prevent direct access */
if ( !function_exists( 'add_action' ) ):
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
endif;

use WpssUserManager\Admin\WPSSAdminFrontend;
use WpssUserManager\Admin\WPSSUsers;
use WpssUserManager\Admin\WPSSPluginHelper;

/** @var  $template */
$users = $template['args'];
?>
<table class="wpss-user-role-editor-table widefat fixed striped table-view-list users nowrap">
    <caption class="d-none"><?php esc_html_e( 'User Details', 'wpss-ultimate-user-management' ); ?></caption>
    <thead>
    <tr>
        <th scope="col"><?php esc_html_e( '#ID', 'wpss-ultimate-user-management' ); ?></th>
        <th scope="col"><?php esc_html_e( 'User', 'wpss-ultimate-user-management' ); ?></th>
        <th scope="col"><?php esc_html_e( 'Edit', 'wpss-ultimate-user-management' ); ?></th>
    </tr>
    </thead>
    <tbody>
	<?php foreach ( $users as $id => $user ): ?>
		<?php if ( 'total' !== $id ): ?>
            <tr>
                <td><?php echo esc_html( $id ); ?></td>
                <td><?php echo esc_html( $user ); ?></td>
                <td>
                    <span class="wpss-user-edit-link" data-user-id="<?php echo esc_attr( $id ); ?>">
                         <?php esc_html_e( 'Edit', 'wpss-ultimate-user-management' ); ?>
                    </span>
                </td>
            </tr>
		<?php endif; ?>
	<?php endforeach; ?>
    </tbody>
</table>
<hr>
<?php $rpp = (int)WPSSPluginHelper::get_option( 'wpss_user_entries_screen' ); ?>
<div class='wpss-user-paginate'>
	<?php
	$paginate = WPSSUsers::paginate_users( $rpp, esc_html( $users['total'] ) );
	if ( $paginate ):
		echo wp_kses( $paginate, WPSSAdminFrontend::sanitize_output() );
	endif;
	?>
</div>
