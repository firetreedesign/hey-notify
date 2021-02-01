=== Hey Notify ===
Contributors: firetree, danielmilner
Tags: notifications, slack, discord, email
Requires at least: 4.3
Tested up to: 5.6
Requires PHP: 5.3
Stable tag: 1.2.1
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

== Installation ==

1. Upload the `hey-notify` folder to the `/wp-content/plugins/` directory.
2. Activate the Hey Notify plugin through the **Plugins** menu in WordPress.
3. Configure the plugin by going to the **Hey Notify** menu that appears in your WordPress Admin.

== Screenshots ==

1. Notification Settings

== Changelog ==

= 1.2.1 =
* Automatically detect available Custom Post Types.

= 1.2.0 =
* Added new System notification for Plugin Activation, Plugin Deactivation, and Theme Changes.
* Added Custom Post Type notifications.
* For developers: Added filter hooks to the message subjects.
