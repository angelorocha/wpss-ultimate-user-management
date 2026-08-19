<?php
/**
 * User details panel, rendered inside the users table.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSRoles;

/**
 * Args passed in by WPSSAdminFrontend::render().
 *
 * @var array $template
 */
$wpss_user_data = $template['user_data'];
?>
<strong><?php esc_html_e( 'User Details', 'wpss-ultimate-user-management' ); ?>:</strong>
<ul>
	<li>
		<strong><?php esc_html_e( 'Login', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $wpss_user_data['user_login'] ); ?>
	</li>
	<li>
		<strong><?php esc_html_e( 'Email', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $wpss_user_data['user_email'] ); ?>
	</li>
	<li>
		<strong><?php esc_html_e( 'Registered at', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $wpss_user_data['user_registered'] ); ?>
	</li>
</ul>
<table class="wp-list-table widefat fixed striped table-view-list table-user-roles">
	<caption class="d-none"><?php esc_html_e( 'User Details', 'wpss-ultimate-user-management' ); ?></caption>
	<thead>
	<tr>
		<th scope="col"><?php esc_html_e( 'Roles', 'wpss-ultimate-user-management' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $wpss_user_data['user_roles'] as $wpss_role ) : ?>
		<tr class="user-role-<?php echo esc_attr( $wpss_role ); ?>">
			<td><?php echo esc_html( WPSSRoles::get_roles_names( false )[ $wpss_role ] ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<hr>

<strong><?php esc_html_e( 'Add Roles', 'wpss-ultimate-user-management' ); ?>:</strong>
<form method="post" action="" class="wpss-add-role-to-user">
	<div>
		<?php
		foreach ( WPSSRoles::get_roles_names( false ) as $wpss_role_key => $wpss_role_name ) :
			$wpss_key     = esc_html( $wpss_role_key );
			$wpss_name    = esc_html( $wpss_role_name );
			$wpss_checked = ( in_array( $wpss_key, $wpss_user_data['user_roles'], true ) ? ' checked' : '' );
			?>
			<label for="wpss-add-role-to-user-<?php echo esc_attr( $wpss_key ); ?>">
				<input type="checkbox" name="wpss-add-role-to-user[]"
						id="wpss-add-role-to-user-<?php echo esc_attr( $wpss_key ); ?>"
						value="<?php echo esc_attr( $wpss_key ); ?>"<?php echo esc_attr( $wpss_checked ); ?>>
				<?php echo esc_html( $wpss_role_name ); ?>
			</label>
		<?php endforeach; ?>
	</div>
	<button class="button-primary"><?php esc_html_e( 'Add Roles', 'wpss-ultimate-user-management' ); ?></button>
</form>
