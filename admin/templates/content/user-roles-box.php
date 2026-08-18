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

$user_id   = $template['args'] ?? false;
$user_data = get_userdata( $user_id );
$user_name = $user_data->display_name;
?>

<div class="wpss-user-roles-box">
	<span class="wpss-close-roles-box">&times;</span>
	<div class="box-header">
		<strong class="wpss-user-name">
			<span data-user-id="<?php echo esc_attr( $user_id ); ?>">#<?php echo esc_html( $user_id ); ?></span>
			- <?php echo esc_html( $user_name ); ?>
		</strong>
	</div>
	<form method="post" action="">
		<div class="wpss-input-wrapper">
			<?php
			foreach ( WPSSRoles::get_roles_names( false ) as $role_key => $role_name ) :
				$key     = esc_html( $role_key );
				$name    = esc_html( $role_name );
				$checked = ( in_array( $key, $user_data->roles, true ) ? ' checked' : '' );
				?>
				<label for="wpss-add-role-to-user-<?php echo esc_attr( $key ); ?>">
					<input type="checkbox" name="wpss-add-role-to-user[]"
							id="wpss-add-role-to-user-<?php echo esc_attr( $key ); ?>"
							value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ); ?>>
					<?php echo esc_html( $role_name ); ?>
				</label>
			<?php endforeach; ?>
		</div>
		
		<button class="btn btn-primary"><?php esc_html_e( 'Update', 'wpss-ultimate-user-management' ); ?></button>
	</form>
</div><!-- .wpss-user-roles-box -->
<div class="wpss-overlay"></div>