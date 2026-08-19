<?php
/**
 * Content access metabox shown on post/page edit screens, to restrict
 * viewing the content to selected roles.
 *
 * @package wpss-ultimate-user-management
 */

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

use WpssUserManager\Admin\WPSSContentAccess;
use WpssUserManager\Admin\WPSSRoles;

$wpss_roles             = WPSSRoles::get_roles_names( false );
$wpss_access_option_key = WPSSContentAccess::$wpss_post_type_access_key;
$wpss_get_access_meta   = get_post_meta( get_the_ID(), $wpss_access_option_key, true );
if ( $wpss_get_access_meta ) :
	$wpss_get_access_meta = json_decode( $wpss_get_access_meta, true );
else :
	$wpss_get_access_meta = [];
endif;
if ( ! empty( $wpss_roles ) ) : ?>
	<ul>
		<?php foreach ( $wpss_roles as $wpss_role_key => $wpss_role_name ) : ?>
			<li>
				<label for="<?php echo esc_html( "$wpss_access_option_key-$wpss_role_key" ); ?>">
					<input type="checkbox"
							id="<?php echo esc_html( "$wpss_access_option_key-$wpss_role_key" ); ?>"
							name="<?php echo esc_attr( "{$wpss_access_option_key}[]" ); ?>"
							value="<?php echo esc_attr( $wpss_role_key ); ?>"
						<?php echo esc_attr( checked( in_array( $wpss_role_key, $wpss_get_access_meta, true ) ) ); ?>>
					<?php echo esc_attr( $wpss_role_name ); ?>
				</label>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
