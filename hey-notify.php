<?php
/**
 * Plugin Name: Hey Notify
 * Plugin URI: https://heynotifywp.com/
 * Description: Get notified when things happen in WordPress.
 * Version: 1.4.2
 * Author: FireTree Design, LLC <info@firetreedesign.com>
 * Author URI: https://firetreedesign.com/
 * Text Domain: hey-notify
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Hey_Notify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEY_NOTIFY_VERSION', '1.4.2' );
define( 'HEY_NOTIFY_PLUGIN_FILE', __FILE__ );
define( 'HEY_NOTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEY_NOTIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once HEY_NOTIFY_PLUGIN_DIR . 'vendor/autoload.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/upgrades.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/cpt.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/filters.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/fields.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/notifications.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/class-hook.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/class-event.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/class-service.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/scripts.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/rest-api.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/languages.php';

// Events.
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/post/loader.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/page/loader.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/comment/loader.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/user/loader.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/system/loader.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/events/cpt/loader.php';

// Services.
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/services/class-slack.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/services/class-discord.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/services/class-microsoft-teams.php';
require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/services/class-email.php';
