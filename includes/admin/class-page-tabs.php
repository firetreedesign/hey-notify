<?php
/**
 * Hey Notify Admin Page Tabs
 *
 * @package Hey_Notify
 * @since 1.5.0
 */

namespace Hey_Notify\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page_Tabs class
 */
class Page_Tabs {

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'hey_notify_settings_page_tabs', array( $this, 'settings_page_tabs_late' ), 100 );
		add_filter( 'hey_notify_settings_page_tabs', array( $this, 'settings_page_tabs' ) );
		add_filter( 'hey_notify_settings_page_actions', array( $this, 'settings_page_actions' ) );
	}

	/**
	 * Settings Page Tabs
	 *
	 * @since 1.5.0
	 * @param  array $tabs Tabs array.
	 * @return array       New tabs array
	 */
	public function settings_page_tabs( $tabs ) {

		$tabs[] = array(
			'tab_id'      => 'general',
			'settings_id' => 'hey_notify_settings_general',
			'title'       => __( 'General', 'hey-notify' ),
			'submit'      => true,
		);

		return $tabs;
	}

	/**
	 * Settings Page Tabs Late
	 *
	 * @since 1.5.0
	 *
	 * @param  array $tabs Tabs array.
	 *
	 * @return array       New tabs array
	 */
	public function settings_page_tabs_late( $tabs ) {

		if ( has_filter( 'hey_notify_license_keys' ) ) {
			$tabs[] = array(
				'tab_id'      => 'licenses',
				'settings_id' => 'hey_notify_settings_licenses',
				'title'       => __( 'Licenses', 'hey-notify' ),
				'submit'      => true,
			);
		}

		$tabs[] = array(
			'tab_id'      => 'uninstall',
			'settings_id' => 'hey_notify_settings_uninstall',
			'title'       => __( 'Uninstall', 'hey-notify' ),
			'submit'      => true,
		);

		return $tabs;
	}

	/**
	 * Settings Page Actions
	 *
	 * @since 1.5.0
	 * @param array $actions Actions array.
	 * @return array New actions array
	 */
	public function settings_page_actions( $actions ) {

		$actions[] = array(
			'tab_id' => 'licenses',
			'class'  => null,
			'link'   => 'https://heynotifywp.com/account/',
			'target' => '_blank',
			'title'  => __( 'Your Account', 'hey-notify' ),
		);

		return $actions;
	}
}

new Page_Tabs();
