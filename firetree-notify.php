<?php
/**
 * Plugin Name: FireTree Notify
 * Plugin URI: https://firetreedesign.com/
 * Description: Send notifications to Slack and Discord.
 * Version: 0.1.0
 * Author: FireTree Design, LLC <info@firetreedesign.com>
 * Author URI: https://firetreedesign.com/
 * Text Domain: firetree-notify
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package FireTree_Notify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FIRETREE_NOTIFY_VERSION', '0.1.0' );
define( 'FIRETREE_NOTIFY_PLUGIN_FILE', __FILE__ );
define( 'FIRETREE_NOTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FIRETREE_NOTIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'vendor/autoload.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/cpt.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/filters.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/events/post.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/events/page.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/fields.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/notifications.php';
require_once FIRETREE_NOTIFY_PLUGIN_DIR . 'includes/services/discord.php';