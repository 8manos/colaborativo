=== YD FeedWordPress Content Filter ===
Contributors: ydubois
Donate link: http://www.yann.com/
Tags: posts, plugin, automatic, Post, English, image, images, FeedWordPress, feed, rss, agregator, filter, syndicate, syndication, autoblogging, content, strip, strip tags, aggregation, aggregator
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: trunk

Description: A filter for the FeedWordPress content syndication plugin.

== Description ==

This plugin is an add-on to the FeedWordPress RSS content syndication plugin.

You need to have FeedWordPress installed and configured for content syndication.

= Fetch images locally as attachments =

If there are images in the syndicated content, they will be fetched as local attachments.

= Automatically resolve redirected target URLs =

Feed entries that point to 301 or 302 redirected URLs will be resolved and replaced with the final target URI.
This way you always get a link to the original source of the syndicated content instead of a link to some in-between redirection script. 

= Normalize content layout =

The content of syndicated posts will be normalized: HTML tags will be removed, duplication of the title will be removed, 
images will be processed to be moved at beginning of content block.

White space and line breaks at beginning and end of content will be removed.

Includes specific targeted filters for content from Google News feeds, Yahoo News feeds and Wikio aggregated feeds.

= Active support =

Drop me a line on my [YD FeedWordPress Content Filter support site](http://www.yann.com/en/wp-plugins/yd-feedwordpress-content-filter "Yann Dubois' FeedWordPress Content Filter for WordPress") to report bugs, ask for a specific feature or improvement, or just tell me how you're using the plugin.
It's still in an active development stage, with new features coming out on a regular basis.

= Credits =

Thanks to [Alessandro Nuzzo](http://www.e-one.it) for providing the image parent attachment routine included in version 0.2.0.

== Installation ==

WordPress automatic installation is fully supported and recommended.

== Frequently Asked Questions ==

= Where should I ask questions? =

http://www.yann.com/en/wp-plugins/yd-feedwordpress-content-filter

Use comments.

I will answer only on that page so that all users can benefit from the answer. 
So please come back to see the answer or subscribe to that page's post comments.

= Puis-je poser des questions et avoir des docs en français ? =

Oui, l'auteur est français.
("but alors... you are French?")

= What is your e-mail address? =

It is mentioned in the comments at the top of the main plugin file. However, please prefer comments on the plugin page (as indicated above) for all non-private matters.

== Screenshots ==

Not yet.

== Revisions ==

* 0.1.0. Initial beta release of 2010/10/26
* 0.2.0. Bugfixes + options 2010/11/24

== Changelog ==

= 0.1.0 =
* Initial release
= 0.2.0 =
* Refactored using the YD Plugins Framework
* Added some options
* Added image attachment hack contributed by Alessandro Nuzzo
* Added redirected URL resolution
* Got rid of unused code
* Small bug fixes

== Upgrade Notice ==

= 0.1.0 =
* No specifics. Automatic upgrade works fine.
= 0.2.0 =
* No specifics. Automatic upgrade works fine.

== Did you like it? ==

Drop me a line on http://www.yann.com/en/wp-plugins/yd-feedwordpress-content-filter

And... *please* rate this plugin --&gt;