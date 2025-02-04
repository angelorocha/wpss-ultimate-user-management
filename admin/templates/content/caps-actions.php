<?php
/** Prevent direct access */
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSCaps;
use WpssUserManager\Admin\WPSSRoles;

/** @var $template */
$role_caps = $template['args'];
?>

<strong>
	<?php
	echo sprintf(
	/* Translators: %s is a role name */
		esc_html__( 'Select the capabilities to the Role %s:', 'wpss-ultimate-user-management' ),
		esc_html( WPSSRoles::get_roles_names()[ $role_caps ] )
	); ?>
</strong>
<hr>
<form method="post" action="" class="wpss-add-caps-to-role">
	<?php
	$admin_caps              = WPSSCaps::get_caps( 'admin' );
	$post_type_caps          = WPSSCaps::get_caps( 'post_type' );
	$tax_caps                = WPSSCaps::get_caps( 'tax' );
	$search_capability_label = __( 'Search Capability', 'wpss-ultimate-user-management' );
	
	/**
	 * Closure to get input checkbox checked param
	 *
	 * @param string $cap
	 *
	 * @return string
	 */
	$checked = function( string $cap ) use ( $role_caps ): string {
		return ( in_array( $cap, WPSSCaps::get_cap_by_role( $role_caps ) ) ? ' checked' : '' );
	};
	?>
    <div>
        <div class="admin-caps caps-list">
            <strong><?php esc_html_e( 'Admin:', 'wpss-ultimate-user-management' ); ?></strong>
            <label for="search-admin-cap" class="d-none">
				<?php esc_html_e( 'Search admin cap', 'wpss-ultimate-user-management' ); ?>
            </label>
            <input id="search-admin-cap" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $search_capability_label ); ?>">
            <div>
                <ul>
					<?php foreach ( array_unique( $admin_caps ) as $admin_cap ): ?>
                        <li>
                            <label for="admin-<?php echo esc_attr( $admin_cap ); ?>">
                                <input type="checkbox" name="wpss-caps-to-role[]"
                                       id="admin-<?php echo esc_attr( $admin_cap ); ?>"
                                       value="<?php echo esc_attr( $admin_cap ); ?>"<?php echo esc_attr( $checked( $admin_cap ) ); ?>>
								<?php echo esc_html( $admin_cap ); ?>
                            </label>
                        </li>
					<?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="post-type-caps caps-list">
            <strong><?php esc_html_e( 'Post Types:', 'wpss-ultimate-user-management' ); ?></strong>
            <label for="search-cpt-caps" class="d-none">
				<?php echo esc_html__( 'Search cpt cap', 'wpss-ultimate-user-management' ); ?>
            </label>
            <input id="search-cpt-caps" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $search_capability_label ); ?>">
            <div>
				<?php
				foreach ( $post_type_caps as $post => $caps ):
					$post_type_label = get_post_type_object( $post )->label; ?>
                    <ul>
                        <li class="caps-container">
                            <strong>
                                <label for="<?php echo esc_attr( $post ); ?>">
                                    <input type="checkbox" id="<?php echo esc_attr( $post ); ?>">
									<?php echo esc_html( $post_type_label ); ?>
                                </label>
                            </strong>
                            <ul>
								<?php foreach ( array_unique( $caps ) as $cap ): ?>
                                    <li>
                                        <label for="<?php echo esc_attr( "$post-$cap" ); ?>">
                                            <input type="checkbox" name="wpss-caps-to-role[]"
                                                   id="<?php echo esc_attr( "$post-$cap" ); ?>"
                                                   value="<?php echo esc_attr( $cap ); ?>"<?php echo esc_attr( $checked( $cap ) ); ?>>
											<?php echo esc_html( $cap ); ?>
                                        </label>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
				<?php endforeach; ?>
            </div>
        </div>
        
        <div class="tax-caps caps-list">
            <strong><?php esc_html_e( 'Taxonomies:', 'wpss-ultimate-user-management' ); ?></strong>
            <label for="search-tax-caps" class="d-none"><?php esc_html_e( 'Search tax cap', 'wpss-ultimate-user-management' ); ?></label>
            <input id="search-tax-caps" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $search_capability_label ); ?>">
            <div>
				<?php
				foreach ( $tax_caps as $tax => $tax_cap ):
					$taxonomy_label = get_taxonomy( $tax )->label; ?>
                    <ul>
                        <li class="caps-container">
                            <strong>
                                <label for="<?php echo esc_attr( $tax ); ?>"><input type="checkbox" id="<?php echo esc_attr( $tax ); ?>">
									<?php echo esc_html( $taxonomy_label ); ?>
                                </label>
                            </strong>
                            <ul>
								<?php foreach ( array_unique( $tax_cap ) as $t_cap ): ?>
                                    <li>
                                        <label for="<?php echo esc_attr( "$tax-$t_cap" ); ?>">
                                            <input type="checkbox" name="wpss-caps-to-role[]"
                                                   id="<?php echo esc_attr( "$tax-$t_cap" ); ?>"
                                                   value="<?php echo esc_attr( $t_cap ); ?>"<?php echo esc_attr( $checked( $t_cap ) ); ?>>
											<?php echo esc_html( $t_cap ); ?>
                                        </label>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <hr>
    <button class="button-primary"><?php esc_html_e( 'Add', 'wpss-ultimate-user-management' ); ?></button>
</form>
