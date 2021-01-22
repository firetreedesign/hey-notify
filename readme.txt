=== Hey Notify ===
Contributors: firetree,danielmilner
Tags: notifications, slack, discord, email
Requires at least: 4.3
Tested up to: 5.6
Requires PHP: 5.3
Stable tag: 1.1.6
License: GPLv2 or later
License URI: http://ww.gnu.org/licenses/gpl-2.0.html

Get notified when things happen in WordPress.

== Description ==

Get notified when things happen in WordPress.

= Notifications can be sent to: =

* Slack
* Discord
* Email

= Notifications for: =

* Posts - Status transitions
* Pages - Status transitions
* Comments - New comments
* Users - New users, admin logins, failed admin logins
* System Events - WordPress, plugin, and theme updates

== Installation ==

1. Upload the `hey-notify` folder to the `/wp-content/plugins/` directory.
2. Activate the Hey Notify plugin through the **Plugins** menu in WordPress.
3. Configure the plugin by going to the **Hey Notify** menu that appears in your WordPress Admin.

== Screenshots ==

1. Notification Settings

== Changelog ==

= 1.1.6 =
* Removed some unneeded vendor files from the previous release.

= 1.1.5 =
* Fixed an issue where System events could only send notifications to one service.

= 1.1.4 =
* Fixed an issue with the Discord service.

= 1.1.3 =
* Fixed Discord message titles.
* Fixed an error with WordPress Core update checks.
* Added an action for interacting with the Settings container.

= 1.1.2 =
* Removed parentheses surrounding new version numbers in theme and plugin update notifications.

= 1.1.0 =
* Added comment notifications.
* Added user notifications.
* Added system notifications.

= 1.0.0 =
* Initial release.