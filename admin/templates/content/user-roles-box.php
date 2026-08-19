<?php
/**
 * Role assignment box for a single user.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSRoles;

$wpss_user_id   = $template['args'] ?? false;
$wpss_user_data = get_userdata( $wpss_user_id );
$wpss_user_name = $wpss_user_data->display_name;
?>

<div class="wpss-user-roles-box">
	<span class="wpss-close-roles-box">&times;</span>
	<div class="box-header">
		<strong class="wpss-user-name">
			<span data-user-id="<?php echo esc_attr( $wpss_user_id ); ?>">#<?php echo esc_html( $wpss_user_id ); ?></span>
			- <?php echo esc_html( $wpss_user_name ); ?>
		</strong>
	</div>
	<form method="post" action="">
		<div class="wpss-input-wrapper">
			<?php
			foreach ( WPSSRoles::get_roles_names( false ) as $wpss_role_key => $wpss_role_name ) :
				$wpss_key     = esc_html( $wpss_role_key );
				$wpss_name    = esc_html( $wpss_role_name );
				$wpss_checked = ( in_array( $wpss_key, $wpss_user_data->roles, true ) ? ' checked' : '' );
				?>
				<label for="wpss-add-role-to-user-<?php echo esc_attr( $wpss_key ); ?>">
					<input type="checkbox" name="wpss-add-role-to-user[]"
							id="wpss-add-role-to-user-<?php echo esc_attr( $wpss_key ); ?>"
							value="<?php echo esc_attr( $wpss_key ); ?>"<?php echo esc_attr( $wpss_checked ); ?>>
					<?php echo esc_html( $wpss_role_name ); ?>
				</label>
			<?php endforeach; ?>
		</div>
		
		<button class="btn btn-primary"><?php esc_html_e( 'Update', 'wpss-ultimate-user-management' ); ?></button>
	</form>
</div><!-- .wpss-user-roles-box -->
<div class="wpss-overlay"></div>
