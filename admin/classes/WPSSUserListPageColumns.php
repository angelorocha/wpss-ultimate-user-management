<?php
/**
 * Manages WordPress user screen columns.
 *
 * @package wpss-ultimate-user-management
 */

namespace WpssUserManager\Admin;

use WP_User_Query;

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

/**
 * Class WPSSUserListPageColumns allow order user list by alphabetical and role order.
 *
 * @since 1.3.0
 */
class WPSSUserListPageColumns {

	/**
	 * Instance of this class.
	 *
	 * @var object|null
	 * @since 1.3.0
	 */
	protected static ?object $instance = null;

	/**
	 * Allow order user by name or role.
	 *
	 * @since 1.3.0
	 */
	protected function __construct() {
		add_filter( 'manage_users_sortable_columns', [ $this, 'link_name_role_user_columns' ] );
		add_action( 'pre_get_users', [ $this, 'pre_get_user_query' ] );
	}

	/**
	 * Get class instance
	 *
	 * @return object
	 * @since 1.0.0
	 */
	public static function instance(): object {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Allow columns to be ordered by name or role.
	 *
	 * @param array $columns List of columns.
	 * @return array
	 * @since 1.3.0
	 */
	public function link_name_role_user_columns( array $columns ): array {
		$columns['name'] = 'name';
		$columns['role'] = 'role';
		return $columns;
	}

	/**
	 * Configure pre get users to order users by name or role.
	 *
	 * @param WP_User_Query $query WP_User_Query object.
	 * @return void
	 *
	 * @since 1.3.0
	 */
	public function pre_get_user_query( WP_User_Query $query ): void {
		if ( ! is_admin() ) {
			return;
		}
		$orderby = $query->get( 'orderby' );
		if ( 'name' === $orderby ) {
			$query->set( 'meta_key', 'first_name' );
			$query->set( 'orderby', 'meta_value' );
		} elseif ( 'role' === $orderby ) {
			global $wpdb;
			$cap_key  = $wpdb->get_blog_prefix() . 'capabilities';
			$relation = [
				'relation'       => 'OR',
				'role_clause'    => array(
					'key'     => $cap_key,
					'compare' => 'EXISTS',
				),
				'no_role_clause' => array(
					'key'     => $cap_key,
					'compare' => 'NOT EXISTS',
				),
			];
			$query->set( 'meta_query', $relation );
			$query->set( 'orderby', 'role_clause' );
		}
	}
}
