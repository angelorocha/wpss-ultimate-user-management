<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSRoles;

/** @var $template */
$user_data = $template['user_data'];
?>
<strong><?php esc_html_e( 'User Details', 'wpss-ultimate-user-management' ); ?>:</strong>
<ul>
    <li>
        <strong><?php esc_html_e( 'Login', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $user_data['user_login'] ); ?>
    </li>
    <li>
        <strong><?php esc_html_e( 'Email', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $user_data['user_email'] ); ?>
    </li>
    <li>
        <strong><?php esc_html_e( 'Registered at', 'wpss-ultimate-user-management' ); ?></strong>:
		<?php echo esc_html( $user_data['user_registered'] ); ?>
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
	<?php foreach ( $user_data['user_roles'] as $role ): ?>
        <tr class="user-role-<?php echo esc_attr( $role ); ?>">
            <td><?php echo esc_html( WPSSRoles::get_roles_names( false )[ $role ] ); ?></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>

<hr>

<strong><?php esc_html_e( 'Add Roles', 'wpss-ultimate-user-management' ); ?>:</strong>
<form method="post" action="" class="wpss-add-role-to-user">
    <div>
		<?php foreach ( WPSSRoles::get_roles_names( false ) as $role_key => $role_name ):
			$key = esc_html( $role_key );
			$name = esc_html( $role_name );
			$checked = ( in_array( $key, $user_data['user_roles'] ) ? ' checked' : '' ); ?>
            <label for="wpss-add-role-to-user-<?php echo esc_attr( $key ); ?>">
                <input type="checkbox" name="wpss-add-role-to-user[]"
                       id="wpss-add-role-to-user-<?php echo esc_attr( $key ); ?>"
                       value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ); ?>>
				<?php echo esc_html( $role_name ); ?>
            </label>
		<?php endforeach; ?>
    </div>
    <button class="button-primary"><?php esc_html_e( 'Add Roles', 'wpss-ultimate-user-management' ); ?></button>
</form>
