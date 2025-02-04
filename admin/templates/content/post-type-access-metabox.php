<?php

use WpssUserManager\Admin\WPSSContentAccess;
use WpssUserManager\Admin\WPSSRoles;

$wpss_roles             = WPSSRoles::get_roles_names( false );
$wpss_access_option_key = WPSSContentAccess::$wpss_post_type_access_key;
$get_access_meta        = get_post_meta( get_the_ID(), $wpss_access_option_key, true );
if ( $get_access_meta ):
	$get_access_meta = json_decode( $get_access_meta, true );
else:
	$get_access_meta = [];
endif;
if ( !empty( $wpss_roles ) ): ?>
    <ul>
		<?php foreach ( $wpss_roles as $wpss_role_key => $wpss_role_name ): ?>
            <li>
                <label for="<?php echo esc_html( "$wpss_access_option_key-$wpss_role_key" ) ?>">
                    <input type="checkbox"
                           id="<?php echo esc_html( "$wpss_access_option_key-$wpss_role_key" ) ?>"
                           name="<?php echo esc_attr( "{$wpss_access_option_key}[]" ); ?>"
                           value="<?php echo esc_attr( $wpss_role_key ); ?>"
						<?php echo esc_attr( checked( in_array( $wpss_role_key, $get_access_meta ) ) ); ?>>
					<?php echo esc_attr( $wpss_role_name ); ?>
                </label>
            </li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>