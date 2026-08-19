<?php
/**
 * Admin menu visibility settings tab, configured per role.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSAdminPages;
use WpssUserManager\Admin\WPSSUserRolesCapsManager;
use WpssUserManager\Admin\RoleCraftRequest;
use WpssUserManager\Admin\WPSSRoles;

$wpss_nonce = wp_create_nonce( WPSSUserRolesCapsManager::nonce() );
?>
<form method="POST" action="" class="wpss-menage-admin-menus">
	<p>
		<?php esc_html_e( 'Select the role to edit access to administrative menu items. This option only hides the menu items, it does not remove the role capability.', 'wpss-ultimate-user-management' ); ?>
	</p>
	<hr>
	<label for="wpss-roles-list">
		<strong><?php esc_html_e( 'Select role:', 'wpss-ultimate-user-management' ); ?></strong>
	</label>
	<?php
	$wpss_checked     = [];
	$wpss_selected    = '';
	$wpss_remove_menu = RoleCraftRequest::post( 'wpss-get-role-to-remove-menu', false, $wpss_nonce );
	if ( ! empty( $wpss_remove_menu ) ) :
		$wpss_selected = $wpss_remove_menu;
		if ( isset( WPSSAdminPages::get_option()[ $wpss_remove_menu ] ) ) :
			$wpss_checked = WPSSAdminPages::get_option()[ $wpss_remove_menu ];
		endif;
	endif;
	?>
	<select name="wpss-get-role-to-remove-menu" id="wpss-roles-list" onchange="this.form.submit()">
		<option value="">------------------</option>
		<?php
		foreach ( WPSSRoles::get_roles_names( false ) as $wpss_role => $wpss_name ) :
			$wpss_role_value  = $wpss_role;
			$wpss_is_selected = $wpss_selected === $wpss_role ? ' selected' : '';
			?>
			<option value="<?php echo esc_attr( $wpss_role_value ); ?>" <?php echo esc_attr( $wpss_is_selected ); ?>>
				<?php echo esc_html( $wpss_name ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	
	<label for="select-all" class="select-all">
		<input type="checkbox" id="select-all"> <?php esc_html_e( 'Select all', 'wpss-ultimate-user-management' ); ?>
	</label>
	<hr>
	<ul class="pages-list">
		<?php
		$wpss_menus = WPSSAdminPages::get_menu_list();
		$wpss_count = 0;
		foreach ( $wpss_menus as $wpss_key => $wpss_val ) :
			++$wpss_count;
			$wpss_check = ( in_array( $wpss_key, $wpss_checked, true ) ? ' checked' : '' );
			?>
			<li>
				<label for="menu-item-<?php echo esc_attr( "{$wpss_count}" ); ?>">
					<input type="checkbox" id="menu-item-<?php echo esc_attr( "{$wpss_count}" ); ?>"
							name="wpss-show-menu-item[]"
							value="<?php echo esc_attr( $wpss_key ); ?>"<?php echo esc_attr( $wpss_check ); ?>>
					<?php echo esc_html( $wpss_val ); ?>
				</label>
			</li>
			<?php
		endforeach;
		$wpss_count = 0;
		?>
	</ul>
	<div class="role-editor-messages d-none"></div>
	<p class="text-center">
		<button class="button-primary"><?php esc_html_e( 'Remove Menu', 'wpss-ultimate-user-management' ); ?></button>
	</p>
</form>
