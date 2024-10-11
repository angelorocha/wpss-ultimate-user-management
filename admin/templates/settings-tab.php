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
	<?php esc_html_e( 'Define plugin settings', 'wpss-ultimate-user-management' ); ?>
</p>
<hr>

<form action="" method="post" class="wpss-settings-tab-form">
    <label for="wpss-default-role-select">
		<?php esc_html_e( 'When user have no roles, assign user in this role: ', 'wpss-ultimate-user-management' ); ?>
        <select required="required" id="wpss-default-role-select" name="wpss_default_role">
			<?php foreach ( WPSSRoles::get_roles_names( false ) as $role => $name ):
				$selected = WPSSPluginHelper::get_option( 'wpss_default_role' ) === $role ? 'selected' : ''; ?>
                <option value='<?php echo esc_attr( $role ); ?>' <?php echo esc_attr( $selected ); ?>>
					<?php echo esc_html( $name ); ?>
                </option>
			<?php endforeach; ?>
        </select>
        <p>
            <small><?php esc_html_e( 'This option works only if the user have only one role and it was deleted.', 'wpss-ultimate-user-management' ); ?></small>
        </p>
    </label>
    
    <label for="wpss-user-entries-limit">
		<?php esc_html_e( 'Number of entries on User Management screen: ', 'wpss-ultimate-user-management' ); ?>
        <input type="number"
               id="wpss-user-entries-limit"
               name="wpss_user_entries_screen"
               min="10"
               value="<?php echo esc_attr( WPSSPluginHelper::get_option( 'wpss_user_entries_screen' ) ); ?>">
    </label>
	
	
	<?php esc_html_e( 'Delete all plugin data on deactivate: ', 'wpss-ultimate-user-management' ); ?>
    <div class="radio-container">
		<?php
		$radio_option_values = [
			0 => __( 'No', 'wpss-ultimate-user-management' ),
			1 => __( 'Yes', 'wpss-ultimate-user-management' ),
		];
		foreach ( $radio_option_values as $val => $label ):
			$checked = (int)WPSSPluginHelper::get_option( 'wpss_delete_plugin_data' ) === $val ? 'checked' : ''; ?>
            <label for="wpss-plugin-data-<?php echo esc_attr( $val ); ?>">
                <input type='radio'
                       name='wpss_delete_plugin_data'
                       id='wpss-plugin-data-<?php echo esc_attr( $val ); ?>'
                       value='<?php echo esc_attr( $val ); ?>'
					<?php echo esc_attr( $checked ); ?>>
				<?php echo esc_html( $label ); ?>
            </label>
		<?php endforeach; ?>
    </div>
    
    <hr>
    <strong>
		<?php esc_html_e( 'Add this roles to new users:', 'wpss-ultimate-user-management' ); ?>
    </strong>
    <div class="new-users-roles">
		<?php
		$roles = WPSSRoles::get_roles_names( false );
		if ( !empty( $roles ) ):
			unset( $roles['administrator'] );
			unset( $roles['subscriber'] );
			$get_users_roles = WPSSPluginHelper::get_option( 'wpss_roles_to_new_users' );
			if ( !empty( $get_users_roles ) ):
				$get_users_roles = json_decode( $get_users_roles, true );
			else:
				$get_users_roles = [];
			endif;
			foreach ( $roles as $key => $role ): ?>
                <label for="user-role-<?php echo esc_attr( $key ); ?>">
                    <input type="checkbox" name="wpss_roles_to_new_users[]"
                           id="user-role-<?php echo esc_attr( $key ); ?>"
                           value="<?php echo esc_attr( $key ); ?>"
						<?php echo esc_attr( checked( !in_array( $key, $get_users_roles ), '', false ) ); ?>>
					<?php echo esc_html( $role ); ?>
                </label>
			<?php endforeach; ?>
		<?php endif; ?>
    </div>
    <hr>
    <strong>
		<?php esc_html_e( 'Enable content access control to this post types:', 'wpss-ultimate-user-management' ); ?>
    </strong>
    <div class="new-users-roles">
		<?php
		$wpss_post_types = get_post_types( [ 'public' => true ] );
		if ( !empty( $wpss_post_types['attachment'] ) ):
			unset( $wpss_post_types['attachment'] );
		endif;
		$get_access_cpt = WPSSPluginHelper::get_option( 'wpss_cpt_access_control' );
		if ( !empty( $get_access_cpt ) ):
			$get_access_cpt = json_decode( $get_access_cpt, true );
		else:
			$get_access_cpt = [];
		endif;
		foreach ( $wpss_post_types as $cpt_key => $wpss_post_type ): ?>
            <label for="wpss_cpt_access_control_<?php echo esc_attr( $cpt_key ); ?>">
                <input type="checkbox"
                       id="wpss_cpt_access_control_<?php echo esc_attr( $cpt_key ); ?>"
                       name="wpss_cpt_access_control[]" value="<?php echo esc_attr( $cpt_key ); ?>"
					<?php echo esc_attr( checked( !in_array( $cpt_key, $get_access_cpt ), '', false ) ); ?>>
				<?php echo esc_attr( get_post_type_object( $cpt_key )->label ); ?>
            </label>
		<?php endforeach; ?>
    </div>
	<?php
	$access_message = WPSSPluginHelper::get_option( 'wpss_cpt_access_message' );
	?>
    <label for="wpss_cpt_access_message">
		<?php esc_html_e( 'Type a message to show when user no have access to content:', 'wpss-ultimate-user-management' ); ?>
    </label>
    <textarea id="wpss_cpt_access_message" name="wpss_cpt_access_message" rows="5" cols="100"><?php echo wp_kses_post( $access_message ); ?></textarea>
    <hr>
    <strong>
		<?php esc_html_e( 'Hide admin bar to this roles:', 'wpss-ultimate-user-management' ); ?>
    </strong>
    <div class="new-users-roles">
		<?php
		$get_hide_admin_bar = WPSSPluginHelper::get_option( 'wpss_hide_admin_bar' );
		if ( !empty( $get_hide_admin_bar ) ):
			$get_hide_admin_bar = json_decode( $get_hide_admin_bar, true );
		else:
			$get_hide_admin_bar = [];
		endif;
		$admin_bar_roles = WPSSRoles::get_roles_names( false );
		foreach ( $admin_bar_roles as $key => $role ): ?>
            <label for="hide-admin-bar-<?php echo esc_attr( $key ); ?>">
                <input type="checkbox" name="wpss_hide_admin_bar[]"
                       id="hide-admin-bar-<?php echo esc_attr( $key ); ?>"
                       value="<?php echo esc_attr( $key ); ?>"
					<?php echo esc_attr( checked( !in_array( $key, $get_hide_admin_bar ), '', false ) ); ?>>
				<?php echo esc_html( $role ); ?>
            </label>
		<?php endforeach; ?>
    </div>
    <hr>
    <div class="settings-message d-none">
		<?php esc_html_e( 'Options saved successfully!', 'wpss-ultimate-user-management' ); ?>
    </div>
    <div class="text-center">
        <button class="button-primary">
			<?php esc_html_e( 'Save Settings', 'wpss-ultimate-user-management' ); ?>
        </button>
    </div>
</form>