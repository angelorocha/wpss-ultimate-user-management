<?php
/**
 * General plugin settings tab.
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

?>
<p>
	<?php esc_html_e( 'Define plugin settings', 'wpss-ultimate-user-management' ); ?>
</p>
<hr>

<form action="" method="post" class="wpss-settings-tab-form">
	<label for="wpss-default-role-select">
		<?php esc_html_e( 'When user have no roles, assign user in this role: ', 'wpss-ultimate-user-management' ); ?>
		<select required="required" id="wpss-default-role-select" name="wpss_default_role">
			<?php
			foreach ( WPSSRoles::get_roles_names( false ) as $wpss_role => $wpss_name ) :
				$wpss_selected = WPSSPluginHelper::get_option( 'wpss_default_role' ) === $wpss_role ? 'selected' : '';
				?>
				<option value='<?php echo esc_attr( $wpss_role ); ?>' <?php echo esc_attr( $wpss_selected ); ?>>
					<?php echo esc_html( $wpss_name ); ?>
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
		$wpss_radio_option_values = [
			0 => __( 'No', 'wpss-ultimate-user-management' ),
			1 => __( 'Yes', 'wpss-ultimate-user-management' ),
		];
		foreach ( $wpss_radio_option_values as $wpss_val => $wpss_label ) :
			$wpss_checked = (int) WPSSPluginHelper::get_option( 'wpss_delete_plugin_data' ) === $wpss_val ? 'checked' : '';
			?>
			<label for="wpss-plugin-data-<?php echo esc_attr( "{$wpss_val}" ); ?>">
				<input type='radio'
						name='wpss_delete_plugin_data'
						id='wpss-plugin-data-<?php echo esc_attr( "{$wpss_val}" ); ?>'
						value='<?php echo esc_attr( "{$wpss_val}" ); ?>'
					<?php echo esc_attr( $wpss_checked ); ?>>
				<?php echo esc_html( $wpss_label ); ?>
			</label>
		<?php endforeach; ?>
	</div>
	
	<hr>
	<strong>
		<?php esc_html_e( 'Add this roles to new users:', 'wpss-ultimate-user-management' ); ?>
	</strong>
	<div class="new-users-roles">
		<?php
		$wpss_roles = WPSSRoles::get_roles_names( false );
		if ( ! empty( $wpss_roles ) ) :
			unset( $wpss_roles['administrator'] );
			unset( $wpss_roles['subscriber'] );
			$wpss_get_users_roles = WPSSPluginHelper::get_option( 'wpss_roles_to_new_users' );
			if ( ! empty( $wpss_get_users_roles ) ) :
				$wpss_get_users_roles = json_decode( $wpss_get_users_roles, true );
			else :
				$wpss_get_users_roles = [];
			endif;
			foreach ( $wpss_roles as $wpss_key => $wpss_role ) :
				?>
				<label for="user-role-<?php echo esc_attr( $wpss_key ); ?>">
					<input type="checkbox" name="wpss_roles_to_new_users[]"
							id="user-role-<?php echo esc_attr( $wpss_key ); ?>"
							value="<?php echo esc_attr( $wpss_key ); ?>"
						<?php echo esc_attr( checked( ! in_array( $wpss_key, $wpss_get_users_roles, true ), '', false ) ); ?>>
					<?php echo esc_html( $wpss_role ); ?>
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
		if ( ! empty( $wpss_post_types['attachment'] ) ) :
			unset( $wpss_post_types['attachment'] );
		endif;
		$wpss_get_access_cpt = WPSSPluginHelper::get_option( 'wpss_cpt_access_control' );
		if ( ! empty( $wpss_get_access_cpt ) ) :
			$wpss_get_access_cpt = json_decode( $wpss_get_access_cpt, true );
		else :
			$wpss_get_access_cpt = [];
		endif;
		foreach ( $wpss_post_types as $wpss_cpt_key => $wpss_post_type ) :
			?>
			<label for="wpss_cpt_access_control_<?php echo esc_attr( $wpss_cpt_key ); ?>">
				<input type="checkbox"
						id="wpss_cpt_access_control_<?php echo esc_attr( $wpss_cpt_key ); ?>"
						name="wpss_cpt_access_control[]" value="<?php echo esc_attr( $wpss_cpt_key ); ?>"
					<?php echo esc_attr( checked( ! in_array( $wpss_cpt_key, $wpss_get_access_cpt, true ), '', false ) ); ?>>
				<?php echo esc_attr( get_post_type_object( $wpss_cpt_key )->label ); ?>
			</label>
		<?php endforeach; ?>
	</div>
	<?php
	$wpss_access_message = WPSSPluginHelper::get_option( 'wpss_cpt_access_message' );
	?>
	<label for="wpss_cpt_access_message">
		<?php esc_html_e( 'Type a message to show when user no have access to content:', 'wpss-ultimate-user-management' ); ?>
	</label>
	<?php
	$wpss_editor_config = [
		'textarea_rows' => 5,
		'quicktags'     => false,
	];
	wp_editor( wp_kses_post( $wpss_access_message ), 'wpss_cpt_access_message', $wpss_editor_config );
	?>
	<hr>
	<strong>
		<?php esc_html_e( 'Hide admin bar to this roles:', 'wpss-ultimate-user-management' ); ?>
	</strong>
	<div class="new-users-roles">
		<?php
		$wpss_get_hide_admin_bar = WPSSPluginHelper::get_option( 'wpss_hide_admin_bar' );
		if ( ! empty( $wpss_get_hide_admin_bar ) ) :
			$wpss_get_hide_admin_bar = json_decode( $wpss_get_hide_admin_bar, true );
		else :
			$wpss_get_hide_admin_bar = [];
		endif;
		$wpss_admin_bar_roles = WPSSRoles::get_roles_names( false );
		foreach ( $wpss_admin_bar_roles as $wpss_key => $wpss_role ) :
			?>
			<label for="hide-admin-bar-<?php echo esc_attr( $wpss_key ); ?>">
				<input type="checkbox" name="wpss_hide_admin_bar[]"
						id="hide-admin-bar-<?php echo esc_attr( $wpss_key ); ?>"
						value="<?php echo esc_attr( $wpss_key ); ?>"
					<?php echo esc_attr( checked( ! in_array( $wpss_key, $wpss_get_hide_admin_bar, true ), '', false ) ); ?>>
				<?php echo esc_html( $wpss_role ); ?>
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
