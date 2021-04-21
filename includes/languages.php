<?php
/**
 * Language setup
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Languages;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions.
add_action( 'init', __NAMESPACE__ . '\\load_textdomain' );

/**
 * Load the textdomain
 *
 * @return void
 */
function load_textdomain() {
	\load_plugin_textdomain( 'hey-notify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
