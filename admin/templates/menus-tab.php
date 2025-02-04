<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSAdminPages;
use WpssUserManager\Admin\WPSSPostGet;
use WpssUserManager\Admin\WPSSRoles;

?>
<form method="POST" action="" class="wpss-menage-admin-menus">
    <p>
		<?php esc_html_e( 'Select the role to edit access to administrative menu items. This
        option only hides the menu items, it does not remove the role capability.', 'wpss-ultimate-user-management' ); ?>
    </p>
    <hr>
    <label for="wpss-roles-list">
        <strong><?php esc_html_e( 'Select role:', 'wpss-ultimate-user-management' ); ?></strong>
    </label>
	<?php
	$checked     = [];
	$selected    = '';
	$remove_menu = WPSSPostGet::post( 'wpss-get-role-to-remove-menu' );
	if ( !empty( $remove_menu ) ):
		$selected = $remove_menu;
		if ( isset( WPSSAdminPages::get_option()[ $remove_menu ] ) ):
			$checked = WPSSAdminPages::get_option()[ $remove_menu ];
		endif;
	endif;
	?>
    <select name="wpss-get-role-to-remove-menu" id="wpss-roles-list" onchange="this.form.submit()">
        <option value="">------------------</option>
		<?php
		foreach ( WPSSRoles::get_roles_names( false ) as $role => $name ):
			$role_value = $role;
			$is_selected = $selected === $role ? ' selected' : ''; ?>
            <option value="<?php echo esc_attr( $role_value ); ?>" <?php echo esc_attr( $is_selected ); ?>>
				<?php echo esc_html( $name ); ?>
            </option>
		<?php endforeach; ?>
    </select>
    
    <label for="select-all" class="select-all">
        <input type="checkbox" id="select-all"> <?php esc_html_e( 'Select all', 'wpss-ultimate-user-management' ); ?>
    </label>
    <hr>
    <ul class="pages-list">
		<?php
		$menus = WPSSAdminPages::get_menu_list();
		$count = 0;
		foreach ( $menus as $key => $val ):
			$count++;
			$check = ( in_array( $key, $checked ) ? ' checked' : '' );
			?>
            <li>
                <label for="menu-item-<?php echo esc_attr( $count ); ?>">
                    <input type="checkbox" id="menu-item-<?php echo esc_attr( $count ); ?>"
                           name="wpss-show-menu-item[]"
                           value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $check ); ?>>
					<?php echo esc_html( $val ); ?>
                </label>
            </li>
		<?php
		endforeach;
		$count = 0;
		?>
    </ul>
    <div class="role-editor-messages d-none"></div>
    <p class="text-center">
        <button class="button-primary"><?php esc_html_e( 'Remove Menu', 'wpss-ultimate-user-management' ); ?></button>
    </p>
</form>
