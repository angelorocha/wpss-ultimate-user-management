<?php
/**
 * Capabilities tab: select a role, then add or remove its capabilities.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSRoles;

?>
<p>
	<?php esc_html_e( 'Select the role to add capabilities.', 'wpss-ultimate-user-management' ); ?>
</p>
<hr>
<form method="post" action="" class="wpss-role-select">
	<label>
		<strong><?php esc_html_e( 'Select the role', 'wpss-ultimate-user-management' ); ?>: </strong>
		<select required="required" id="wpss-role-select" name="wpss-role-select">
			<option value="">-----------</option>
			<?php
			$wpss_get_system_roles = WPSSRoles::get_roles_names( false );
			unset( $wpss_get_system_roles['administrator'] );
			foreach ( $wpss_get_system_roles as $wpss_role => $wpss_name ) :
				?>
				<option value="<?php echo esc_attr( $wpss_role ); ?>"><?php echo esc_attr( $wpss_name ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
</form><!-- .wpss-role-select -->

<div class="role-editor-messages d-none">
</div><!-- .role-editor-messages -->

<div class="wpss-role-caps-container d-none">
	<hr>
	<div></div>
</div><!-- .wpss-role-caps-container -->
