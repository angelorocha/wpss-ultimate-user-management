<?php
/**
 * Renders the paginated users table.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSAdminFrontend;
use WpssUserManager\Admin\WPSSUsers;
use WpssUserManager\Admin\WPSSPluginHelper;

/**
 * Args passed in by WPSSAdminFrontend::render().
 *
 * @var array $template
 */
$wpss_users = $template['args'];
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
	<?php foreach ( $wpss_users as $wpss_id => $wpss_user ) : ?>
		<?php if ( 'total' !== $wpss_id ) : ?>
			<tr>
				<td><?php echo esc_html( $wpss_id ); ?></td>
				<td><?php echo esc_html( $wpss_user ); ?></td>
				<td>
					<span class="wpss-user-edit-link" data-user-id="<?php echo esc_attr( $wpss_id ); ?>">
						<?php esc_html_e( 'Edit', 'wpss-ultimate-user-management' ); ?>
					</span>
				</td>
			</tr>
		<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>
<hr>
<?php $wpss_rpp = (int) WPSSPluginHelper::get_option( 'wpss_user_entries_screen' ); ?>
<div class='wpss-user-paginate'>
	<?php
	$wpss_paginate = WPSSUsers::paginate_users( $wpss_rpp, (int) $wpss_users['total'] );
	if ( $wpss_paginate ) :
		echo wp_kses( $wpss_paginate, WPSSAdminFrontend::sanitize_output() );
	endif;
	?>
</div>
