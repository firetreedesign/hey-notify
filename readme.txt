=== Hey Notify ===
Contributors: firetree, danielmilner
Tags: notifications, alert, slack, discord, email, microsoft teams, ninja forms, gravity forms
Requires at least: 4.3
Tested up to: 5.8
Requires PHP: 5.3
Stable tag: 1.4.2
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

= Hey Notify Add-ons =

Premium add-ons are available to extend the capabilities of Hey Notify.

* [Gravity Forms](https://heynotifywp.com/add-ons/gravity-forms/)
* [Ninja Forms](https://heynotifywp.com/add-ons/ninja-forms/)

== Installation ==

1. Upload the `hey-notify` folder to the `/wp-content/plugins/` directory.
2. Activate the Hey Notify plugin through the **Plugins** menu in WordPress.
3. Configure the plugin by going to the **Hey Notify** menu that appears in your WordPress Admin.

== Screenshots ==

1. Notification Settings

== Changelog ==

= 1.4.2 =
* Fixed an issue with Post/Page/CPT Updated notifications being triggered.

= 1.4.1 =
* Fixed a reference to a function that did not exist.

= 1.4.0 =
* Added support for Microsoft Teams.
* Improved the type of status changes than can be detected for Posts, Pages, and Custom Post Types.
* Reorganized the Settings page.
