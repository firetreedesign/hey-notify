=== Hey Notify ===
Contributors: firetree, danielmilner
Tags: notifications, alert, slack, discord, email
Requires at least: 4.3
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 2.1.0
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

* Posts
    * Draft
    * Pending
    * Scheduled
    * Published
    * Trashed
* Pages
    * Draft
    * Pending
    * Scheduled
    * Published
    * Trashed
* Custom Post Types
    * Draft
    * Pending
    * Scheduled
    * Published
    * Trashed
* Comments
    * New Comment
* Users
    * New User
    * Administrator Login
    * Failed Administrator Login
* System Events
    * WordPress Updates
    * Plugin Updates
    * Plugin Activation
    * Plugin Deactivation
    * Theme Updates
    * Theme Change

= Hey Notify Pro =

Stay in the know with [Hey Notify Pro](https://heynotifywp.com/pro/). Premium features to keep you up to date with everything happening on your website.

* Customize notification messages.
* Native integration with Gravity Forms.
* Native integration with Ninja Forms.
* Sales notifications from Easy Digital Downloads.

== Installation ==

1. Upload the `hey-notify` folder to the `/wp-content/plugins/` directory.
2. Activate the Hey Notify plugin through the **Plugins** menu in WordPress.
3. Configure the plugin by going to the **Hey Notify** menu that appears in your WordPress Admin.

== Screenshots ==

1. Notification Settings
2. Hey Notify Settings Page

== Changelog ==

= 2.1.0 =
* Microsoft Teams support has been removed due to changes with Office 365 connectors.
* Changes to improve Hey Notify Pro capabilities.

= 2.0.3 =
* Added support for Live Preview in the Plugin Directory.
* Expanded admin header to add and edit notification pages.
* Updated language file for translations.

= 2.0.2 =
* Fixed version.

= 2.0.1 =
* Changed network requests to use `wp_safe_remote_post`.

= 2.0.0 =
* BREAKING CHANGES - If you use our Gravity Forms or Ninja Forms add-on, please download our new Pro add-on from your [Account](https://heynotifywp.com/account/) as the individual plugins have been discontinued.
* Migrated settings from Carbon Fields to our own custom fields.
* Settings page redesign.
* Notification event tags now show their complete event name.
* Updated Slack settings to reflect their new webhook API.
* New Comment notifications are not sent if comment is marked as spam.
