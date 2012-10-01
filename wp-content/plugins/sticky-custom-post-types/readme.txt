=== Sticky Custom Post Types ===
Contributors: superann
Donate link: http://superann.com/donate/?id=WP+Sticky+Custom+Post+Types+plugin
Tags: custom post types, sticky
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables support for sticky custom post types.

== Description ==

This plugin adds a "Stick this to the front page" checkbox on the admin add/edit entry page of selected custom post types.

Set options to enable in Settings → Reading. Unless you're using custom queries to display your sticky posts, you probably want to check the option to add selected post types to the blog home.

Note: Sticky custom posts are stored in the global 'sticky_posts' option field, just like regular posts.

== Installation ==

1. Upload `sticky-custom-post-types.php` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to 'Settings → Reading' and set your options.

== Frequently Asked Questions ==

None.

== Screenshots ==

None.

== Changelog ==

= 1.2.3 =
* Added contributions by [Viper007Bond](http://www.viper007bond.com) including enhanced posts filter (now affects main query only and will respect other post types) and formatting updates to conform with the official WordPress Coding Standards doc in the codex.
* Home query no longer modified at all if "Display selected post type(s) on home page" option is left unchecked (note that this means that checked sticky custom posts will show up in the home feed but non-sticky custom posts will be left out of the normal flow).

= 1.2.2 =
* Added custom post types to paged blog home.

= 1.2.1 =
* Fixed filter method (only applies when suppress_filters is false).

= 1.2 =
* Modified filter method to control display of selected custom post types on the blog home, and added an option to allow the user to enable/disable the filter.
* Moved plugin settings from 'Settings → Writing' to 'Settings → Reading'.

= 1.1 =
* Moved plugin settings from 'Settings → General' to 'Settings → Writing'.

= 1.0 =
* Initial version.

== Upgrade Notice ==

= 1.2.3 =
Activating the posts filter function on home now affects main query only and respects other post types, which should make it play more nicely with other plugins.