=== Hey Notify ===
Contributors: firetree, danielmilner
Tags: notifications, alert, slack, discord, email, microsoft teams, ninja forms, gravity forms
Requires at least: 4.3
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://ww.gnu.org/licenses/gpl-2.0.html

Get notified when things happen in WordPress.

== Description ==

Get notified when things happen in WordPress.

= Notifications can be sent to: =

* Slack
* Discord
* Microsoft Teams
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

= 2.0.0 =
* BREAKING CHANGES - If you use our Gravity Forms or Ninja Forms add-on, please download our new Pro add-on from your [Account](https://heynotifywp.com/account/) as the individual plugins have been discontinued.
* Migrated settings from Carbon Fields to our own custom fields.
* Settings page redesign.
* Notification event tags now show their complete event name.
* Updated Slack settings to reflect their new webhook API.
* New Comment notifications are not sent if comment is marked as spam.

= 1.4.2 =
* Fixed an issue with Post/Page/CPT Updated notifications being triggered.

= 1.4.1 =
* Fixed a reference to a function that did not exist.

= 1.4.0 =
* Added support for Microsoft Teams.
* Improved the type of status changes than can be detected for Posts, Pages, and Custom Post Types.
* Reorganized the Settings page.
