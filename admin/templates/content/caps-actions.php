<?php
/**
 * Renders the capabilities checklist form for a selected role.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSCaps;
use WpssUserManager\Admin\WPSSRoles;

/**
 * Args passed in by WPSSAdminFrontend::render().
 *
 * @var array $template
 */
$wpss_role_caps = $template['args'];
?>

<strong>
	<?php
	printf(
	/* Translators: %s is a role name */
		esc_html__( 'Select the capabilities to the Role %s:', 'wpss-ultimate-user-management' ),
		esc_html( WPSSRoles::get_roles_names()[ $wpss_role_caps ] )
	);
	?>
</strong>
<hr>
<form method="post" action="" class="wpss-add-caps-to-role">
	<?php
	$wpss_admin_caps              = WPSSCaps::get_caps( 'admin' );
	$wpss_post_type_caps          = WPSSCaps::get_caps( 'post_type' );
	$wpss_tax_caps                = WPSSCaps::get_caps( 'tax' );
	$wpss_search_capability_label = __( 'Search Capability', 'wpss-ultimate-user-management' );

	/**
	 * Closure to get input checkbox checked param
	 *
	 * @param string $wpss_cap
	 *
	 * @return string
	 */
	$wpss_checked = function ( string $wpss_cap ) use ( $wpss_role_caps ): string {
		return ( in_array( $wpss_cap, WPSSCaps::get_cap_by_role( $wpss_role_caps ), true ) ? ' checked' : '' );
	};
	?>
	<div>
		<div class="admin-caps caps-list">
			<strong><?php esc_html_e( 'Admin:', 'wpss-ultimate-user-management' ); ?></strong>
			<label for="search-admin-cap" class="d-none">
				<?php esc_html_e( 'Search admin cap', 'wpss-ultimate-user-management' ); ?>
			</label>
			<input id="search-admin-cap" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $wpss_search_capability_label ); ?>">
			<div>
				<ul>
					<?php foreach ( array_unique( $wpss_admin_caps ) as $wpss_admin_cap ) : ?>
						<li>
							<label for="admin-<?php echo esc_attr( $wpss_admin_cap ); ?>">
								<input type="checkbox" name="wpss-caps-to-role[]"
										id="admin-<?php echo esc_attr( $wpss_admin_cap ); ?>"
										value="<?php echo esc_attr( $wpss_admin_cap ); ?>"<?php echo esc_attr( $wpss_checked( $wpss_admin_cap ) ); ?>>
								<?php echo esc_html( $wpss_admin_cap ); ?>
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
			<input id="search-cpt-caps" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $wpss_search_capability_label ); ?>">
			<div>
				<?php
				foreach ( $wpss_post_type_caps as $wpss_post => $wpss_caps ) :
					$wpss_post_type_label = get_post_type_object( $wpss_post )->label;
					?>
					<ul>
						<li class="caps-container">
							<strong>
								<label for="<?php echo esc_attr( $wpss_post ); ?>">
									<input type="checkbox" id="<?php echo esc_attr( $wpss_post ); ?>">
									<?php echo esc_html( $wpss_post_type_label ); ?>
								</label>
							</strong>
							<ul>
								<?php foreach ( array_unique( $wpss_caps ) as $wpss_cap ) : ?>
									<li>
										<label for="<?php echo esc_attr( "$wpss_post-$wpss_cap" ); ?>">
											<input type="checkbox" name="wpss-caps-to-role[]"
													id="<?php echo esc_attr( "$wpss_post-$wpss_cap" ); ?>"
													value="<?php echo esc_attr( $wpss_cap ); ?>"<?php echo esc_attr( $wpss_checked( $wpss_cap ) ); ?>>
											<?php echo esc_html( $wpss_cap ); ?>
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
			<input id="search-tax-caps" class="cap-filter" type="text" placeholder="<?php echo esc_attr( $wpss_search_capability_label ); ?>">
			<div>
				<?php
				foreach ( $wpss_tax_caps as $wpss_tax => $wpss_tax_cap ) :
					$wpss_taxonomy_label = get_taxonomy( $wpss_tax )->label;
					?>
					<ul>
						<li class="caps-container">
							<strong>
								<label for="<?php echo esc_attr( $wpss_tax ); ?>"><input type="checkbox" id="<?php echo esc_attr( $wpss_tax ); ?>">
									<?php echo esc_html( $wpss_taxonomy_label ); ?>
								</label>
							</strong>
							<ul>
								<?php foreach ( array_unique( $wpss_tax_cap ) as $wpss_t_cap ) : ?>
									<li>
										<label for="<?php echo esc_attr( "$wpss_tax-$wpss_t_cap" ); ?>">
											<input type="checkbox" name="wpss-caps-to-role[]"
													id="<?php echo esc_attr( "$wpss_tax-$wpss_t_cap" ); ?>"
													value="<?php echo esc_attr( $wpss_t_cap ); ?>"<?php echo esc_attr( $wpss_checked( $wpss_t_cap ) ); ?>>
											<?php echo esc_html( $wpss_t_cap ); ?>
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
