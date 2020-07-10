<?php
/**
 * Custom Post Type
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\CPT;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions
add_action( 'init', __NAMESPACE__ . '\\register_post_type' );

// Filters
add_filter( 'use_block_editor_for_post_type', __NAMESPACE__ . '\\disable_block_editor', 10, 2 );
add_filter( 'gutenberg_can_edit_post_type', __NAMESPACE__ . '\\disable_block_editor', 10, 2 );

/**
 * Register the Custom Post Type
 * 
 * @since 1.0.0
 * @return void
 */
function register_post_type() {
	$labels = array(
		'name'                => _x( 'Notifications', 'Post Type General Name', 'firetree-notify' ),
		'singular_name'       => _x( 'Notification', 'Post Type Singular Name', 'firetree-notify' ),
		'menu_name'           => __( 'FireTree Notify', 'firetree-notify' ),
		'parent_item_colon'   => __( 'Parent Notification:', 'firetree-notify' ),
		'all_items'           => __( 'All Notifications', 'firetree-notify' ),
		'view_item'           => __( 'View Notification', 'firetree-notify' ),
		'add_new_item'        => __( 'Add New Notification', 'firetree-notify' ),
		'add_new'             => __( 'Add New', 'firetree-notify' ),
		'edit_item'           => __( 'Edit Notification', 'firetree-notify' ),
		'update_item'         => __( 'Update Notification', 'firetree-notify' ),
		'search_items'        => __( 'Search Notifications', 'firetree-notify' ),
		'not_found'           => __( 'Not found', 'firetree-notify' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'firetree-notify' ),
	);

	$args = array(
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'show_in_rest'        => false,
		'menu_position'       => null,
		'menu_icon'           => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDUwIDUwIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPgogICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMSwwLDAsMSwtMTE1LjkzMSwtMzEuMTk1OSkiPgogICAgICAgIDxwYXRoIGQ9Ik0xNTMuMjI1LDYxLjAzN0MxNTMuMjQxLDYyLjE5OCAxNTMuMTY3LDYzLjM4NCAxNTMuMDA1LDY0LjYyNEMxNTIuNDU2LDY4Ljg2IDE1MC43NDcsNzIuMzU0IDE0Ny43NDgsNzUuMzcxQzE0Ni45MzYsNzYuMTkgMTQ2LjMyNSw3Ni42OTggMTQ1LjQ3Myw3Ny4yNjlDMTQ0Ljg1MSw3Ny42OSAxNDMuNjgyLDc4LjMwOCAxNDMuMDcxLDc4LjUzOUwxNDIuNjU2LDc4LjcwMUwxNDIuMiw3OC4zMjFDMTQxLjI4OSw3Ny41NjEgMTQwLjI2OCw3Ni4zODYgMTM5LjY1MSw3NS4zNzJDMTM5LjIzNSw3NC43MDQgMTM4LjYzNSw3My40MDIgMTM4LjM4Niw3Mi42MzZDMTM3Ljc4NSw3MC43ODEgMTM3LjU3MSw2OS4zNCAxMzcuNTY5LDY3LjEwNEMxMzcuNTY3LDY1LjIzMSAxMzcuNzczLDYzLjM5OCAxMzguMTYzLDYxLjgwOEMxMzguMjQ0LDYxLjQ3OSAxMzguMzA3LDYxLjE5NyAxMzguMjk1LDYxLjE5MUMxMzguMjg0LDYxLjE3OSAxMzguMDY1LDYxLjI4OSAxMzcuODEyLDYxLjQzM0MxMzYuNzExLDYyLjA1NyAxMzUuMjY2LDYzLjMzMSAxMzQuNDQzLDY0LjM5OEMxMzIuNzM5LDY2LjYxOSAxMzEuOTUyLDY5LjI2NCAxMzIuMTYyLDcyLjExN0MxMzIuMzEzLDc0LjI2MSAxMzIuODA1LDc2LjAyOSAxMzMuOTg5LDc4LjY3OUMxMzQuMDQxLDc4Ljc5NSAxMzMuOTE0LDc4Ljc2NiAxMzIuODUzLDc4LjQwOUMxMzEuMTQxLDc3Ljg0IDEyOS45NTMsNzcuMzIzIDEyOC43NzEsNzYuNjM4QzEyNy44MjYsNzYuMDkxIDEyNy4yNjEsNzUuNjYgMTI2LjUxMSw3NC45QzEyNC44MDksNzMuMTcyIDEyMy43ODIsNzEuMzU4IDEyMy4zMzYsNjkuMjc4QzEyMy4xNTcsNjguNDU0IDEyMy4xMTUsNjYuNzEzIDEyMy4yNDcsNjUuNzYyQzEyMy41MTYsNjMuNzk3IDEyNC4zNDksNjEuNjAxIDEyNS42NSw1OS40MTVDMTI1LjkxNSw1OC45NzcgMTI2LjU4Miw1Ny45MzkgMTI3LjEzNSw1Ny4xMTVDMTI4LjQ1OCw1NS4xMzcgMTI4LjkzLDU0LjE2OCAxMjkuNDAxLDUyLjQ0NUMxMjkuNzIzLDUxLjI1NyAxMjkuODAzLDUwLjY0IDEyOS44MDIsNDkuMjc1QzEyOS44MDEsNDcuOTU1IDEyOS43NDMsNDcuNTU3IDEyOS40MTksNDYuNTg5QzEyOS4yMjMsNDUuOTk2IDEyOS4yMjMsNDYuMDE5IDEyOS4zNTUsNDYuMDE5QzEyOS42MDksNDYuMDE5IDEzMC44MjYsNDYuOTM0IDEzMS44MDYsNDcuODYxQzEzMi42Niw0OC42NzMgMTMzLjQ3OSw0OS42ODEgMTM0LjAxNiw1MC42MDJDMTM0LjA3Myw1MC43IDEzNC4wODUsNTAuNyAxMzQuMTcxLDUwLjU2MkMxMzQuMzE1LDUwLjM0MyAxMzQuNDMsNDkuNjk3IDEzNC41MDQsNDguNjg4QzEzNC42MTIsNDcuMjAyIDEzNC40ODQsNDUuMjk0IDEzNC4xNzEsNDMuNzY3QzEzMy41NDYsNDAuNzI1IDEzMS4zODEsMzYuNzY4IDEyOC45NywzNC4yNTdDMTI4LjY3NiwzMy45NTIgMTI4LjQzNCwzMy42ODcgMTI4LjQzNCwzMy42N0MxMjguNDM0LDMzLjYxOCAxMjguNTMyLDMzLjY1OCAxMjkuNDcyLDM0LjA5QzEzMi4zNjYsMzUuNDEzIDEzNS41MTQsMzcuNDEgMTM4LjAyMywzOS41MTdDMTM5LjA2Nyw0MC4zOTIgMTQyLjA4OSw0My40MjYgMTQyLjk2Niw0NC40OEMxNDMuMDAyLDQ0LjUyMyAxNDMuMDM4LDQ0LjU2NyAxNDMuMDc0LDQ0LjYxQzE0MC4xMzEsNDYuMjg4IDEzOC4xOTgsNDkuMTI3IDEzOC4xOTgsNTIuMzM4QzEzOC4xOTgsNTQuNzU4IDEzOS4yODYsNTYuOTg3IDE0MS4xMDcsNTguNjQ1QzE0MS4yOTcsNTguODA4IDE0MS40MDYsNTkuMDggMTQxLjM3OSw1OS4zNTJMMTQwLjg2Miw2My4wNDlDMTQwLjgwOCw2My4zNzUgMTQxLjEzNCw2My41OTIgMTQxLjQwNiw2My40MjlMMTQ0Ljk0LDYxLjIyN0MxNDUuMTU3LDYxLjExOSAxNDUuNDAyLDYxLjA5MSAxNDUuNjE5LDYxLjE0NkMxNDYuNzYxLDYxLjQ3MiAxNDcuOTU3LDYxLjY2MiAxNDkuMjM1LDYxLjY2MkMxNTAuNjQyLDYxLjY2MiAxNTEuOTg4LDYxLjQ0MSAxNTMuMjI1LDYxLjAzN1oiIHN0eWxlPSJmaWxsLXJ1bGU6bm9uemVybzsiLz4KICAgICAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgwLjIzMzY1MiwwLDAsMC4yMzM2NTIsMTM3Ljc1OCw0MS4zNzc4KSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik04OS42LDQ2LjdDODkuNiw2NS42IDcxLjQsODEgNDguOSw4MUM0NC4yLDgxIDM5LjgsODAuMyAzNS42LDc5LjFDMzQuOCw3OC45IDMzLjksNzkgMzMuMSw3OS40TDIwLjEsODcuNUMxOS4xLDg4LjEgMTcuOSw4Ny4zIDE4LjEsODYuMUwyMCw3Mi41QzIwLjEsNzEuNSAxOS43LDcwLjUgMTksNjkuOUMxMi4zLDYzLjggOC4zLDU1LjYgOC4zLDQ2LjdDOC4zLDI3LjggMjYuNSwxMi40IDQ5LDEyLjRDNzEuNCwxMi40IDg5LjYsMjcuOCA4OS42LDQ2LjdaIiBzdHlsZT0iZmlsbC1ydWxlOm5vbnplcm87Ii8+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4K',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
	);

	\register_post_type( 'firetree_notify', $args );
}

/**
 * Disable the block editor
 *
 * @param boolean $is_enabled
 * @param string $post_type
 * @return boolean
 */
function disable_block_editor($is_enabled, $post_type) {
	if ( 'firetree_notify' === $post_type ) {
		return false;
	}
	
	return $is_enabled;
}