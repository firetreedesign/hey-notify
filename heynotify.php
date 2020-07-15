<?php
/**
 * Plugin Name: Hey Notify
 * Plugin URI: https://heynotifywp.com/
 * Description: Send notifications to Slack, Discord, and more.
 * Version: 0.1.0
 * Author: FireTree Design, LLC <info@firetreedesign.com>
 * Author URI: https://firetreedesign.com/
 * Text Domain: heynotify
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package HeyNotify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEYNOTIFY_VERSION', '0.1.0' );
define( 'HEYNOTIFY_PLUGIN_FILE', __FILE__ );
define( 'HEYNOTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEYNOTIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once HEYNOTIFY_PLUGIN_DIR . 'vendor/autoload.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/cpt.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/filters.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/fields.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/notifications.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/class-hook.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/class-events.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/class-service.php';

// Events
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/events/post/class-post-hook.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/events/post/class-post-event.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/events/page/class-page-hook.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/events/page/class-page-event.php';

// Services
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/services/class-discord.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/services/class-slack.php';
require_once HEYNOTIFY_PLUGIN_DIR . 'includes/services/class-email.php';