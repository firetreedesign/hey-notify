<?php
/**
 * Custom Post Type
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\CPT;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions.
add_action( 'init', __NAMESPACE__ . '\\register_post_type' );
add_action( 'manage_hey_notify_posts_custom_column', __NAMESPACE__ . '\\column_content', 10, 2 );
add_action( 'admin_head', __NAMESPACE__ . '\\admin_head' );

// Filters.
add_filter( 'use_block_editor_for_post_type', __NAMESPACE__ . '\\disable_block_editor', 10, 2 );
add_filter( 'gutenberg_can_edit_post_type', __NAMESPACE__ . '\\disable_block_editor', 10, 2 );
add_filter( 'manage_hey_notify_posts_columns', __NAMESPACE__ . '\\column_titles' );

/**
 * Register the Custom Post Type
 *
 * @since 1.0.0
 * @return void
 */
function register_post_type() {
	$labels = array(
		'name'               => _x( 'Notifications', 'Post Type General Name', 'hey-notify' ),
		'singular_name'      => _x( 'Notification', 'Post Type Singular Name', 'hey-notify' ),
		'menu_name'          => __( 'Hey Notify', 'hey-notify' ),
		'parent_item_colon'  => __( 'Parent Notification:', 'hey-notify' ),
		'all_items'          => __( 'All Notifications', 'hey-notify' ),
		'view_item'          => __( 'View Notification', 'hey-notify' ),
		'add_new_item'       => __( 'Add New Notification', 'hey-notify' ),
		'add_new'            => __( 'Add New', 'hey-notify' ),
		'edit_item'          => __( 'Edit Notification', 'hey-notify' ),
		'update_item'        => __( 'Update Notification', 'hey-notify' ),
		'search_items'       => __( 'Search Notifications', 'hey-notify' ),
		'not_found'          => __( 'Not found', 'hey-notify' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'hey-notify' ),
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
		'menu_icon'           => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgNTAwIDUwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cGF0aCBkPSJNIDE1OC43MDcgMTgyLjAyNSBDIDE3Ni44NCAxODIuMDI1IDE4NS45MDYgMTkxLjAxNiAxODUuOTA2IDIwOC45OTggTCAxODUuOTA2IDI4MS44NDYgTCAxNTYuOTU5IDI4MS44NDYgTCAxNTYuOTU5IDIxOC4wNTUgQyAxNTYuOTU5IDIxMy43MjQgMTU2LjIxMSAyMTAuNzQgMTU0LjcxOCAyMDkuMTAzIEMgMTUzLjIzMiAyMDcuNDU5IDE1MC42MTIgMjA2LjYzNiAxNDYuODU2IDIwNi42MzYgQyAxNDIuNzExIDIwNi42MzYgMTM4LjQzNyAyMDcuNDIzIDEzNC4wMzQgMjA4Ljk5OCBDIDEzMC4zMDcgMjEwLjMzMSAxMjUuODM5IDIxMi4zNjkgMTIwLjYyOCAyMTUuMTEyIEwgMTIwLjYyOCAyODEuODQ2IEwgOTEuODc1IDI4MS44NDYgTCA5MS44NzUgMTQ0LjAyNiBMIDEyMC42MjggMTQ0LjAyNiBMIDEyMC44MjMgMTc3LjY5MyBDIDEyMC44MjMgMTgyLjgxMiAxMjAuNTI5IDE4Ny42NjkgMTE5Ljk0MiAxOTIuMjYzIEMgMTE5LjY3MSAxOTQuNDE2IDExOS4zNzcgMTk2LjQxMSAxMTkuMDYyIDE5OC4yNDYgQyAxMjQuOTM3IDE5My41MjIgMTMwLjg5OSAxODkuNzkxIDEzNi45NDggMTg3LjA1MiBDIDE0NC4zMyAxODMuNzAxIDE1MS41ODMgMTgyLjAyNSAxNTguNzA3IDE4Mi4wMjUgWiBNIDI1NC4wOTggMTgyLjAyNSBDIDI3MC4yODggMTgyLjAyNSAyODEuOTEgMTg0LjgxNyAyODguOTY1IDE5MC4zOTkgQyAyOTYuMDI4IDE5NS45NzQgMjk5LjU2IDIwNC4yNzMgMjk5LjU2IDIxNS4yOTkgQyAyOTkuNjg5IDIyNC4yMjQgMjk3LjQ4NyAyMzAuODgzIDI5Mi45NTQgMjM1LjI3NiBDIDI4OC40MjEgMjM5LjY3NyAyODAuNTg1IDI0MS44NzggMjY5LjQ0NiAyNDEuODc4IEwgMjM1LjI1MyAyNDEuODc4IEMgMjM1LjYwOSAyNDQuODMgMjM2LjEyNyAyNDcuMzU0IDIzNi44MDcgMjQ5LjQ1MiBDIDIzOC4yMzIgMjUzLjg1MyAyNDAuNjYzIDI1Ni45MDcgMjQ0LjA5OSAyNTguNjE0IEMgMjQ3LjUyOCAyNjAuMzIgMjUyLjI4NSAyNjEuMTczIDI1OC4zNzMgMjYxLjE3MyBDIDI2Mi43NzYgMjYxLjE3MyAyNjguMjQ2IDI2MS4wMDYgMjc0Ljc4MyAyNjAuNjc0IEMgMjgxLjMyNyAyNjAuMzUgMjg3LjkwMyAyNTkuNzk1IDI5NC41MDkgMjU5LjAwNyBMIDI5Ny4yMjggMjc2LjkyNCBDIDI5My40NzIgMjc4Ljc2MiAyODkuMTk4IDI4MC4yMDUgMjg0LjQwNiAyODEuMjU2IEMgMjc5LjYxNCAyODIuMzA2IDI3NC42OTIgMjgzLjA1OCAyNjkuNjQxIDI4My41MTMgQyAyNjQuNTg5IDI4My45NzYgMjU5LjczMiAyODQuMjA5IDI1NS4wNyAyODQuMjA5IEMgMjQyLjg5NSAyODQuMjA5IDIzMy4wODYgMjgyLjQwNyAyMjUuNjQzIDI3OC44MDEgQyAyMTguMTkyIDI3NS4xODcgMjEyLjgxMSAyNjkuNjM5IDIwOS41MDUgMjYyLjE1OCBDIDIwNi4yMDYgMjU0LjY3NiAyMDQuNTU3IDI0NS4wOTQgMjA0LjU1NyAyMzMuNDEyIEMgMjA0LjU1NyAyMjAuNjggMjA2LjI0MSAyMTAuNTczIDIwOS42MDggMjAzLjA5MiBDIDIxMi45NzYgMTk1LjYxIDIxOC4yODYgMTkwLjIyOSAyMjUuNTM5IDE4Ni45NDcgQyAyMzIuNzkyIDE4My42NjYgMjQyLjMxMiAxODIuMDI1IDI1NC4wOTggMTgyLjAyNSBaIE0gMjYxLjQ4MSAyMjUuNTM3IEMgMjY1Ljg4NSAyMjUuNTM3IDI2OC42OTkgMjI0LjU4OCAyNjkuOTI2IDIyMi42ODggQyAyNzEuMTYgMjIwLjc4MSAyNzEuNzc4IDIxOC4xMjEgMjcxLjc3OCAyMTQuNzA4IEMgMjcxLjY0OCAyMTAuMTE0IDI3MC4zMTggMjA2LjkzNCAyNjcuNzg5IDIwNS4xNjYgQyAyNjUuMjY3IDIwMy4zOSAyNjEuMDI4IDIwMi41MDEgMjU1LjA3IDIwMi41MDEgQyAyNDkuNzU5IDIwMi41MDEgMjQ1LjYxNSAyMDMuMjIzIDI0Mi42MzYgMjA0LjY2NyBDIDIzOS42NTcgMjA2LjExMSAyMzcuNTg1IDIwOC44NjcgMjM2LjQxOSAyMTIuOTM2IEMgMjM1LjUyNSAyMTYuMDU2IDIzNC45NzMgMjIwLjI1NiAyMzQuNzY1IDIyNS41MzcgWiBNIDM4My4xIDE4NC41ODQgTCA0MTMuNzk2IDE4NC41ODQgTCAzNzkuNDA5IDI4My4wMjcgQyAzNzcuODU0IDI4Ny44ODQgMzc1LjY4OCAyOTIuNzcxIDM3Mi45MDcgMjk3LjY4OSBDIDM3MC4xMTggMzAyLjYxNSAzNjYuNjIxIDMwNy4wNDggMzYyLjQxNiAzMTAuOTg1IEMgMzU4LjIwMiAzMTQuOTIzIDM1My4xNDYgMzE3LjkwNiAzNDcuMjQ5IDMxOS45MzcgQyAzNDEuMzYgMzIxLjk3NSAzMzQuNDAxIDMyMi40NyAzMjYuMzcgMzIxLjQyIEwgMzI0LjIzMyAzMDQuODgyIEMgMzMyLjEzNCAzMDIuNzgyIDMzOC4yODYgMjk5Ljk2IDM0Mi42OSAyOTYuNDE2IEMgMzQ3LjA5NCAyOTIuODcyIDM1MC41MjYgMjg4LjIxMiAzNTIuOTg3IDI4Mi40MzcgTCAzNTMuMjQ4IDI4MS44NDYgTCAzNDcuOTM1IDI4MS44NDYgQyAzNDUuNzM0IDI4MS44NDYgMzQzLjc5MSAyODEuMTkgMzQyLjEwNyAyNzkuODc3IEMgMzQwLjQyMyAyNzguNTY1IDMzOS4yNTggMjc2Ljg1OCAzMzguNjEgMjc0Ljc1OCBMIDMwNi45NDIgMTg0LjU4NCBMIDMzNy42MzkgMTg0LjU4NCBMIDM1NS45MDEgMjQ3LjU4OCBDIDM1Ni40MTkgMjUwLjA4MiAzNTYuOTM3IDI1Mi41NzYgMzU3LjQ1NSAyNTUuMDcgQyAzNTcuOTczIDI1Ny41NjQgMzU4LjQyNyAyNjAuMDU3IDM1OC44MTUgMjYyLjU1MSBMIDM2MS4yMDYgMjYyLjU1MSBDIDM2MS42MTQgMjYxLjIxOSAzNjIuMDE3IDI1OS44MDcgMzYyLjQxNiAyNTguMzEyIEMgMzYzLjUxMyAyNTQuMTgxIDM2NC40NDkgMjUwLjYwNyAzNjUuMjI2IDI0Ny41ODggWiBNIDM5MC42MTkgMzY2LjA1IEwgNDExLjQzNiAzNjYuMDUgQyA0NDYuMzQzIDM2Ni4wNSA0NzQuNjQyIDMzNy4zNzIgNDc0LjY0MiAzMDEuOTk3IEwgNDc0LjY0MiAxNDcuNDgzIEMgNDc0LjY0MiAxMTIuMTA4IDQ0Ni4zNDMgODMuNDMgNDExLjQzNiA4My40MyBMIDg4LjU2MyA4My40MyBDIDUzLjY1NiA4My40MyAyNS4zNTcgMTEyLjEwOCAyNS4zNTcgMTQ3LjQ4MyBMIDI1LjM1NyAzMDEuOTk3IEMgMjUuMzU3IDMzNy4zNzIgNTMuNjU2IDM2Ni4wNSA4OC41NjMgMzY2LjA1IEwgMzAzLjQwOSAzNjYuMDUgTCAzMDMuMTI0IDQxNi41NyBaIiBzdHlsZT0id2hpdGUtc3BhY2U6IHByZTsgZmlsbDogcmdiKDE4MSwgMTgxLCAxODEpOyIvPgogIDxwYXRoIGQ9Ik0gMTUwLjAwOCAyMTcuNjYgQyAxNTcuMDg2IDIxNy42NiAxNjIuNTI5IDIxOS40MzggMTY2LjEwOCAyMjIuOTQgQyAxNjkuNjg4IDIyNi40NDQgMTcxLjQ5NSAyMzEuNzc5IDE3MS41MDggMjM4LjcxIEwgMTcxLjUwOCAyOTQuNzEgTCAxNDguMTU4IDI5NC43MSBMIDE0OC4xNTggMjQ1LjYxIEMgMTQ4LjE2NiAyNDIuMzcyIDE0Ny42NTMgMjQwLjI2NiAxNDYuNTU5IDIzOS4xMjggQyAxNDUuNTIzIDIzNy45NDUgMTQzLjY4NCAyMzcuNDEgMTQwLjg1OCAyMzcuNDEgQyAxMzcuNzAzIDIzNy40MSAxMzQuNDg3IDIzNy45OTUgMTMxLjEyNCAyMzkuMTgxIEMgMTI4LjI2NyAyNDAuMTkgMTI1LjAwMiAyNDEuNjU5IDEyMS4xMDggMjQzLjY3MiBMIDEyMS4xMDggMjk0LjcxIEwgOTcuOTA4IDI5NC43MSBMIDk3LjkwOCAxODguNzEgTCAxMjEuMTA1IDE4OC43MSBMIDEyMS4xMDggMTg5LjIwNyBMIDEyMS4yNTggMjE0Ljg2IEMgMTIxLjI1OCAyMTguNzc1IDEyMS4wMjkgMjIyLjUwNyAxMjAuNTc0IDIyNi4wMjMgQyAxMjAuMzY0IDIyNy42NjkgMTIwLjI2OCAyMjguMjkyIDEyMC4xMDIgMjI5LjMzNiBDIDEyNC42NjIgMjI1LjcyIDEyOC4zMDQgMjIzLjYzNiAxMzMuMDA0IDIyMS41MzQgQyAxMzguNzUgMjE4Ljk2NSAxNDQuNDUyIDIxNy42NiAxNTAuMDA4IDIxNy42NiBaIE0gMTMzLjQxMiAyMjIuNDQ2IEMgMTI4Ljc3MiAyMjQuNTE4IDEyNC4yMjIgMjI3LjMyOCAxMTkuNzEgMjMwLjkxIEwgMTE4LjY4MiAyMzEuNzI2IEwgMTE4LjkwNyAyMzAuNDMzIEMgMTE5LjE0OSAyMjkuMDQgMTE5LjM3MyAyMjcuNTMyIDExOS41ODIgMjI1Ljg5NyBDIDEyMC4wMzMgMjIyLjQxMyAxMjAuMjU4IDIxOC43NDYgMTIwLjI1OCAyMTQuODYxIEwgMTIwLjExMSAxODkuNzEgTCA5OC45MDggMTg5LjcxIEwgOTguOTA4IDI5My43MSBMIDEyMC4xMDggMjkzLjcxIEwgMTIwLjEwOCAyNDMuMDY0IEwgMTIwLjM3OCAyNDIuOTI0IEMgMTI0LjQxMyAyNDAuODI4IDEyNy44OTUgMjM5LjI2MSAxMzAuNzkyIDIzOC4yMzkgQyAxMzQuMjI5IDIzNy4wMjUgMTM3LjYxMyAyMzYuNDEgMTQwLjg1OCAyMzYuNDEgQyAxNDMuODMyIDIzNi40MSAxNDYuMDM4IDIzNy4xMjkgMTQ3LjI5NiAyMzguNDUyIEMgMTQ4LjUwNyAyMzkuODA3IDE0OS4xNSAyNDIuMjQ4IDE0OS4xNTggMjQ1LjYxIEwgMTQ5LjE1OCAyOTMuNzEgTCAxNzAuNTA4IDI5My43MSBMIDE3MC41MDggMjM4LjcxIEMgMTcwLjUyMSAyMzEuOTQxIDE2OC44MjggMjI3LjAwMSAxNjUuNDA4IDIyMy42NTUgQyAxNjEuOTg3IDIyMC4zMDcgMTU2LjkzIDIxOC42NiAxNTAuMDA4IDIxOC42NiBDIDE0NC41NjQgMjE4LjY2IDEzOS4wNjYgMjE5LjkwOSAxMzMuNDEyIDIyMi40NDYgWiBNIDIyMy42NTggMjE3LjY2IEMgMjM2LjIxNiAyMTcuNjYgMjQ1LjM0MiAyMTkuODQ3IDI1MC44ODUgMjI0LjE0NiBDIDI1Ni40MTQgMjI4LjQ5MSAyNTkuMjM3IDIzNS4wMDkgMjU5LjI1OCAyNDMuNTA2IEMgMjU5LjM0NSAyNTAuMzg5IDI1Ny41NjggMjU1LjY0MyAyNTQuMDA0IDI1OS4wOTEgQyAyNTAuMzk4IDI2Mi41MDQgMjQ0LjE3NSAyNjQuMjUxIDIzNS41MDggMjY0LjI2IEwgMjA5LjY3NiAyNjQuMjYgQyAyMDkuOTQzIDI2Ni4yNDQgMjEwLjI2NSAyNjcuODAzIDIxMC43ODMgMjY5LjM3NCBDIDIxMS44NCAyNzIuNjQ4IDIxMy41OSAyNzQuODI5IDIxNi4xNTggMjc2LjA2MSBDIDIxOC43MzkgMjc3LjMzOSAyMjIuMzAyIDI3Ny45NiAyMjYuOTU4IDI3Ny45NiBDIDIzMC4zNSAyNzcuOTYgMjM0LjU2MiAyNzcuODMzIDIzOS42MDQgMjc3LjU4MSBDIDI0NC42NDggMjc3LjMzNCAyNDkuNzA4IDI3Ni45MTMgMjU0LjggMjc2LjMxMyBMIDI1NS4yNzkgMjc2LjI1NyBMIDI1Ny41MDggMjkwLjc1IEwgMjU3LjE3NSAyOTAuOTEgQyAyNTQuMjQ2IDI5Mi4zMjIgMjUwLjg5IDI5My40NDMgMjQ3LjE2NCAyOTQuMjQ5IEMgMjQzLjQ0OCAyOTUuMDUyIDIzOS42MTggMjk1LjYyOSAyMzUuNzAzIDI5NS45NzggQyAyMzEuNzkyIDI5Ni4zMzIgMjI4LjAyIDI5Ni41MSAyMjQuNDA4IDI5Ni41MSBDIDIxNC45NjMgMjk2LjUxIDIwNy4yODIgMjk1LjEwOCAyMDEuNDcyIDI5Mi4zNDEgQyAxOTUuNjQ5IDI4OS41MzggMTkxLjM3NCAyODUuMTg3IDE4OC43NzIgMjc5LjQxNCBDIDE4Ni4yMDYgMjczLjY1NSAxODQuOTA4IDI2Ni4yNTMgMTg0LjkwOCAyNTcuMzEgQyAxODQuOTA4IDI0Ny41NjggMTg2LjIyNiAyMzkuNzYxIDE4OC44NTMgMjM0LjAwMyBDIDE5MS40ODkgMjI4LjIyMyAxOTUuNzIzIDIyMy45OSAyMDEuNDA0IDIyMS40NTMgQyAyMDcuMDYxIDIxOC45MjggMjE0LjUxNiAyMTcuNjYgMjIzLjY1OCAyMTcuNjYgWiBNIDIwMS44MTIgMjIyLjM2NyBDIDE5Ni4yOTMgMjI0LjgzIDE5Mi4zMjcgMjI4Ljc5NyAxODkuNzYzIDIzNC40MTggQyAxODcuMTkgMjQwLjA1OSAxODUuOTA4IDI0Ny42NTIgMTg1LjkwOCAyNTcuMzEgQyAxODUuOTA4IDI2Ni4xNjcgMTg3LjE1NSAyNzMuMzY1IDE4OS42ODQgMjc5LjAwNiBDIDE5Mi4xODggMjg0LjYzMyAxOTYuMjIxIDI4OC43MzYgMjAxLjkwNCAyOTEuNDM5IEMgMjA3LjU4OCAyOTQuMTY2IDIxNS4wNTMgMjk1LjUxIDIyNC40MDggMjk1LjUxIEMgMjI3Ljk5NiAyOTUuNTEgMjMxLjcyNCAyOTUuMzM0IDIzNS42MTMgMjk0Ljk4MiBDIDIzOS40OTggMjk0LjYzNyAyNDMuMjY4IDI5NC4wNjggMjQ2Ljk1MiAyOTMuMjcxIEMgMjUwLjYyNiAyOTIuNDc3IDI1My42MiAyOTEuNDgxIDI1Ni40MDcgMjkwLjE2OSBMIDI1NC40MzcgMjc3LjM2MiBDIDI0OS40ODkgMjc3LjkzNCAyNDQuNzE0IDI3OC4zMzIgMjM5LjY1MiAyNzguNTc5IEMgMjM0LjYgMjc4LjgzMyAyMzAuMzY2IDI3OC45NiAyMjYuOTU4IDI3OC45NiBDIDIyMi4yMTQgMjc4Ljk2IDIxOC40MzEgMjc4LjI4MSAyMTUuNzE4IDI3Ni45NTkgQyAyMTIuOTggMjc1LjU5MSAyMTAuOTc2IDI3My4xMTggMjA5LjgzMyAyNjkuNjg2IEMgMjA5LjMgMjY4LjA2MSAyMDguODkgMjY2LjA5IDIwOC42MTIgMjYzLjgyMSBMIDIwOC41NDQgMjYzLjI2IEwgMjM1LjUwOCAyNjMuMjYgQyAyNDQuMDQxIDI2My4yNjkgMjQ5LjkxOCAyNjEuNjYyIDI1My4zMTIgMjU4LjM2OSBDIDI1Ni43NDggMjU1LjEyMyAyNTguMzcxIDI1MC4yMzQgMjU4LjI1OCAyNDMuNTE3IEMgMjU4LjI3OCAyMzUuMjE2IDI1NS42NDggMjI5LjA4MyAyNTAuMjcxIDIyNC45MzUgQyAyNDQuOTIgMjIwLjcyOCAyMzYuMSAyMTguNjYgMjIzLjY1OCAyMTguNjYgQyAyMTQuNiAyMTguNjYgMjA3LjM1NSAyMTkuODkyIDIwMS44MTIgMjIyLjM2NyBaIE0gMjA4LjIzMiAyNTEuMjkgQyAyMDguMzk1IDI0Ny4yNDQgMjA4LjgyNyAyNDMuOTgzIDIwOS41MjggMjQxLjU3MSBDIDIxMC40NTMgMjM4LjM4NCAyMTIuMTg5IDIzNi4xMDggMjE0LjU5MiAyMzQuOTU5IEMgMjE2Ljk1NCAyMzMuODMgMjIwLjI2NiAyMzMuMjYgMjI0LjQwOCAyMzMuMjYgQyAyMjkuMDU5IDIzMy4yNiAyMzIuNDczIDIzMy45ODcgMjM0LjUxMiAyMzUuMzc5IEMgMjM2LjU1MyAyMzYuODM0IDIzNy42ODggMjM5LjQ3IDIzNy44MDggMjQzLjA2IEMgMjM3LjgwMSAyNDUuNzE3IDIzNy4yODMgMjQ3Ljg4MSAyMzYuMjk2IDI0OS40MTQgQyAyMzUuMjE5IDI1MC45NjEgMjMyLjgzNyAyNTEuNzk3IDIyOS4zNTggMjUxLjgxIEwgMjA4LjIxMSAyNTEuODEgWiBNIDIyOS4zNTggMjUwLjgxIEMgMjMyLjY3OSAyNTAuODIzIDIzNC42NDMgMjUwLjIxMyAyMzUuNDYgMjQ4Ljg2NiBDIDIzNi4zNzggMjQ3LjQ5MyAyMzYuODE0IDI0NS42MDYgMjM2LjgwOCAyNDMuMDY2IEMgMjM2LjcyNiAyMzkuNjYxIDIzNS44MDkgMjM3LjQ0IDIzMy45NDQgMjM2LjIwMiBDIDIzMi4wODkgMjM0Ljg4OCAyMjguOTU3IDIzNC4yNiAyMjQuNDA4IDIzNC4yNiBDIDIyMC4zNSAyMzQuMjYgMjE3LjI2MiAyMzQuNzkgMjE1LjAyNCAyMzUuODYxIEMgMjEyLjgyNyAyMzYuOTEyIDIxMS4zNjMgMjM4LjgzNiAyMTAuNDg4IDI0MS44NDkgQyAyMDkuODA5IDI0NC4xOSAyMDkuNDI3IDI0Ny4wNTcgMjA5LjI1MyAyNTAuODEgWiBNIDM0Ny42NjUgMjE5LjYxIEwgMzIwLjg4MiAyOTUuMjcxIEMgMzE5LjY3MiAyOTguOTk0IDMxNy45OCAzMDIuNzYgMzE1LjgyMiAzMDYuNTI5IEMgMzEzLjY0NyAzMTAuMzE0IDMxMC45IDMxMy43NSAzMDcuNjI3IDMxNi43NzggQyAzMDQuMzMgMzE5LjgxIDMwMC4zMzggMzIyLjEzNiAyOTUuNzM5IDMyMy43MDMgQyAyOTEuMTI5IDMyNS4yNjkgMjg1LjY0MyAzMjUuNjU3IDI3OS4zOTQgMzI0Ljg1NiBMIDI3OS4wMTIgMzI0LjgwNyBMIDI3Ny4yNTUgMzExLjM4OCBMIDI3Ny42ODEgMzExLjI3NiBDIDI4My43NDEgMzA5LjY5IDI4OC4zOTUgMzA3LjU4MSAyOTEuNzQ3IDMwNC45MTggQyAyOTUuMDk3IDMwMi4yNTggMjk3LjY4MSAyOTguODA1IDI5OS41NTIgMjk0LjQ1NSBMIDI5OS40MzcgMjk0LjcxIEwgMjk2LjEwOCAyOTQuNzEgQyAyOTQuMzI5IDI5NC42OTcgMjkyLjY2OCAyOTQuMTU2IDI5MS4zMDMgMjkzLjEwNiBDIDI4OS45NDMgMjkyLjA2IDI4OC45NjggMjkwLjYzMyAyODguNDM0IDI4OC45NzEgTCAyNjMuNzQ5IDIxOS42MSBMIDI4OC41MzIgMjE5LjYxIEwgMjg4LjYzOCAyMTkuOTY5IEwgMzAyLjc0NyAyNjguMDA3IEMgMzAzLjE0NyAyNjkuOTA3IDMwMy41NDcgMjcxLjgwNyAzMDMuOTQ3IDI3My43MDcgQyAzMDQuMzQ5IDI3NS42MTMgMzA0LjY0OSAyNzcuMjQ0IDMwNC45MzQgMjc5LjAxIEwgMzA1Ljk4NSAyNzkuMDEgQyAzMDYuMjYxIDI3OC4xMDUgMzA2LjQ5OCAyNzcuMjg0IDMwNi44MDUgMjc2LjE1IEMgMzA3LjY1MiAyNzMuMDA0IDMwOC4zNzggMjcwLjI3MSAzMDguOTc3IDI2Ny45NzIgTCAzMjIuODgxIDIxOS42MSBaIE0gMzA5Ljk0IDI2OC4yNDIgQyAzMDkuMzQgMjcwLjU0MiAzMDguNjE4IDI3My4yNjIgMzA3Ljc3MSAyNzYuNDEgQyAzMDcuNDYyIDI3Ny41NTMgMzA3LjE0OCAyNzguNjM5IDMwNi44MzIgMjc5LjY1OCBMIDMwNi43MjMgMjgwLjAxIEwgMzA0LjA4MSAyODAuMDEgTCAzMDQuMDE0IDI3OS41ODggQyAzMDMuNzE1IDI3Ny42OTQgMzAzLjM2NyAyNzUuODA3IDMwMi45NjkgMjczLjkxMyBDIDMwMi41NjkgMjcyLjAxMyAzMDIuMTcxIDI3MC4xMjYgMzAxLjc3MyAyNjguMjMyIEwgMjg3Ljc4NCAyMjAuNjEgTCAyNjUuMTY3IDIyMC42MSBMIDI4OS4zNzkgMjg4LjY0MiBDIDI4OS44NDYgMjkwLjE3NSAyOTAuNjczIDI5MS4zNiAyOTEuOTEzIDI5Mi4zMTQgQyAyOTMuMTQ4IDI5My4yNjQgMjk0LjQ4NyAyOTMuNzIzIDI5Ni4xMDggMjkzLjcxIEwgMzAwLjk4MyAyOTMuNzEgTCAzMDAuNDY1IDI5NC44NjIgQyAyOTguNTM2IDI5OS4zMTEgMjk1LjgxOSAzMDIuOTYyIDI5Mi4zNjkgMzA1LjcwMiBDIDI4OC45MjEgMzA4LjQzOSAyODQuMjk5IDMxMC41MjcgMjc4LjM2MSAzMTIuMTMgTCAyNzkuOTA0IDMyMy45MTIgQyAyODUuODkgMzI0LjY0NyAyOTAuOTMzIDMyNC4yOTcgMjk1LjQxNiAzMjIuNzU3IEMgMjk5LjkyNCAzMjEuMjMgMzAzLjc0IDMxOS4wMSAzMDYuOTQ5IDMxNi4wNDIgQyAzMTAuMTcgMzEzLjA3IDMxMi44MjMgMzA5Ljc1MiAzMTQuOTU0IDMwNi4wMzEgQyAzMTcuMDkgMzAyLjMwNiAzMTguNzQyIDI5OC42MyAzMTkuOTMyIDI5NC45NTYgTCAzNDYuMjUxIDIyMC42MSBMIDMyMy42MzUgMjIwLjYxIFoiIHN0eWxlPSJmaWxsOiBub25lOyIgdHJhbnNmb3JtPSJtYXRyaXgoMSwgMCwgMCwgMSwgMCwgMCkiLz4KPC9zdmc+',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
	);

	\register_post_type( 'hey_notify', $args );
}

/**
 * Disable the block editor
 *
 * @since 1.0.0
 * @param boolean $is_enabled Is enabled.
 * @param string  $post_type Post type.
 * @return boolean
 */
function disable_block_editor( $is_enabled, $post_type ) {
	if ( 'hey_notify' === $post_type ) {
		return false;
	}

	return $is_enabled;
}

/**
 * Set up the Custom Post Type Column Titles
 *
 * @since 1.0.0
 * @param array $defaults Default values.
 * @return array
 */
function column_titles( $defaults ) {
	$new_defaults = array();
	foreach ( $defaults as $key => $title ) {
		switch ( $key ) {
			case 'title':
				$new_defaults[ $key ]    = $title;
				$new_defaults['service'] = __( 'Service', 'hey-notify' );
				$new_defaults['events']  = __( 'Events', 'hey-notify' );
				break;
			default:
				$new_defaults[ $key ] = $title;
				break;
		}
	}
	return $new_defaults;
}

/**
 * Echo the Custom Post Type Column Content
 *
 * @since 1.0.0
 * @param string $column_name Column name.
 * @param int    $post_id Post ID.
 * @return void
 */
function column_content( $column_name, $post_id ) {
	switch ( $column_name ) {
		case 'service':
			$services_array = \apply_filters( 'hey_notify_services_options', array() );
			$service        = \carbon_get_post_meta( $post_id, 'hey_notify_service' );
			foreach ( $services_array as $services ) {
				if ( $service === $services['value'] ) {
					echo "<img src='" . esc_attr( $services['image'] ) . "' style='width: 100px; height: auto;' />";
				}
			}
			break;
		case 'events':
			$events = \carbon_get_post_meta( $post_id, 'hey_notify_events' );
			if ( $events ) {
				foreach ( $events as $event ) {
					echo '<span class="wp-ui-primary hey-notify-tag">' . esc_html( ucwords( str_replace( '_', ' ', $event[ $event['type'] ] ) ) ) . '</span>';
				}
			}
			break;
	}
}

/**
 * Content to ouput in the Admin head
 *
 * @return void
 */
function admin_head() {
	global $pagenow;

	if ( 'edit.php' !== $pagenow ) {
		return;
	}

	// phpcs:ignore
	if ( ! isset( $_GET['post_type'] ) ) {
		return;
	}

	// phpcs:ignore
	if ( 'hey_notify' !== $_GET['post_type'] ) {
		return;
	}
	?>
	<style>
		.hey-notify-tag { border-radius: 3px; display: inline-block; margin-bottom: 4px; padding: 3px 6px; font-size: 12px; }
		.hey-notify-tag:not(:last-of-type) { margin-right: 4px; }
	</style>
	<?php
}
