=== Category Order ===
Contributors: wp_guy
Donate link: http://wpguy.com/plugins/category-order
Tags: category, categories, order, reorder
Requires at least: 2.3
Tested up to: 2.5.1
Stable tag: trunk

== Description ==

The Order Categories plugin allows you to easily reorder your categories the way you want via drag and drop.

== Installation ==

To install the plugin just follow these simple steps:

1. Download the plugin and expand it.
2. Copy the 'order-categories.php' file into your plugins folder (wp-content/plugins/).
3. Login into the WordPress administration area and go to the Plugins page.
4. Locate the Order Categories plugin and click on the activate link
5. That's it, now you can go to Manage > Order Categories and reorder the categories the way you want.
6. If you use wp_list_categories(), make sure you don't specify an order (e.g. wp_list_categories('orderby=count');).

== Frequently Asked Questions ==

= Does this plugin require me to overwrite WordPress core files? =

No, there is nothing more you have to do except uploading and activating the plugin.

= Does this plugin allow me to reorder subcategories as well? =

Yes it does. You'll see a 'more >' link next to the categories that have subcategories. Click on that link and you'll be able to reorder those subcategories.

= The Category Order panel seems to work fine but my categories are not reordered on my blog. What's going on? =

Make sure you're not specifying an order in wp_list_categories() (e.g. wp_list_categories('orderby=count');)