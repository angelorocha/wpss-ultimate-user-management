<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSPluginHelper;
use WpssUserManager\Admin\WPSSRoles;

?>
<p>
	<?php
	$default_role = WPSSPluginHelper::get_option( 'wpss_default_role' );
	echo sprintf(
	/* Translators: 1: default role name */
		esc_html__( 'Add or remove custom roles. Native WordPress roles cannot be deleted. When a role is deleted, users who only have this role are moved to the default role (%1$s).',
					'wpss-ultimate-user-management' ), esc_html( $default_role ) );
	?>
</p>
<hr>
<div class="table-container">
    <table class="wp-list-table widefat fixed striped table-view-list table-roles">
        <caption class="d-none"><?php esc_html_e( 'Role management', 'wpss-ultimate-user-management' ); ?></caption>
        <thead>
        <tr>
            <th scope="col"><?php esc_html_e( 'Role', 'wpss-ultimate-user-management' ); ?></th>
            <th scope="col"><?php esc_html_e( 'Users', 'wpss-ultimate-user-management' ); ?></th>
            <th scope="col"><?php esc_html_e( 'Delete', 'wpss-ultimate-user-management' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php
		$get_users_per_role = count_users();
		$protected_roles    = array_diff( WPSSRoles::get_roles_names( false ), WPSSRoles::get_roles_names() );
		foreach ( WPSSRoles::get_roles_names( false ) as $key => $role ):
			$total_users = 0;
			if ( isset( $get_users_per_role['avail_roles'][ $key ] ) ):
				$total_users = $get_users_per_role['avail_roles'][ $key ];
			endif;
			$delete_role_label = __( 'Delete Role', 'wpss-ultimate-user-management' ); ?>
            <tr id='role-<?php echo esc_attr( $key ); ?>' class='role-delete'>
                <td><?php echo esc_html( $role ); ?></td>
                <td><?php echo esc_html( $total_users ) ?></td>
				<?php if ( !in_array( $key, array_keys( $protected_roles ) ) ): ?>
                    <td>
                    <span title='<?php echo esc_attr( $delete_role_label ); ?>'
                          data-role-id='<?php echo esc_attr( $key ); ?>'
                          data-role-name='<?php echo esc_attr( $role ); ?>'>&times;</span>
                    </td>
				<?php else: ?>
                    <td></td>
				<?php endif; ?>
            </tr>
		<?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2"><?php esc_html_e( 'Total Users', 'wpss-ultimate-user-management' ); ?></td>
            <td><?php echo esc_html( $get_users_per_role['total_users'] ); ?></td>
        </tr>
        </tfoot>
    </table>
    
    <div class="role-delete-confirm-msg">
		<?php esc_html_e( 'Are you sure you want to delete the role:', 'wpss-ultimate-user-management' ); ?>
        <p class="text-center role-name"><strong></strong></p>
        <p class="text-center">
            <button class="button-secondary confirm-delete"><?php esc_html_e( 'Delete', 'wpss-ultimate-user-management' ); ?></button>
            <button class="button-primary cancel-delete"><?php esc_html_e( 'Cancel', 'wpss-ultimate-user-management' ); ?></button>
        </p>
        <strong><?php esc_html_e( 'This action cannot be undone.', 'wpss-ultimate-user-management' ); ?></strong>
    </div>
    
    <div class="role-editor-messages d-none"></div>
</div><!-- .table-container -->
<hr>
<form method="post" action="" class="form-roles">
    <label for="wpss-add-new-role">
        <strong><?php esc_html_e( 'Add Role', 'wpss-ultimate-user-management' ); ?>: </strong>
        <input type="text" name="wpss-add-new-role" id="wpss-add-new-role" value="">
        <button class="button-primary"><?php esc_html_e( 'Add', 'wpss-ultimate-user-management' ); ?></button>
    </label>
</form><!-- .form-roles -->
