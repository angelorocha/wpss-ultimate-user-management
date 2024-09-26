<?php

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
			<?php foreach ( WPSSRoles::get_roles_names() as $role => $name ): ?>
                <option value="<?php echo esc_attr( $role ); ?>"><?php echo esc_attr( $name ); ?></option>
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
