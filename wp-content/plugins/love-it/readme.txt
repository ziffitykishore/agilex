=== Love It ===
Author URI: http://pippinsplugins.com
Contributors: mordauk
Donate link: http://pippinsplugins.com/support-the-site
Tags: love it, love, like, plus one, love post, most loved
Requires at least: 3.4
Tested up to: 4.3
Stable tag: 1.0.5

Love It is a simple plugin that adds a "Love It" link to your posts, pages, and custom post types. Show your most popular items in a widget.

== Description ==

Love It is a simple plugin that adds a "Love It" link to your posts, pages, and custom post types. It works similar to Facebook's Like button, but is exclusive to your website. It provides a great way for users to show their appreciation, and for you to gain a good idea of which posts are your most popular.

The plugin includes a simple widget to show your "Most Loved" items.

This plugin is based of the tutorial I published on [creating a simple Love It plugin]( http://pippinsplugins.com/write-a-love-it-plugin-with-ajax/)

The demonstration video for the tutorial and the plugin is below:

[youtube http://www.youtube.com/watch?v=Dskc-BUjKxg]

An improved Pro version of the plugin is [available](https://pippinsplugins.com/love-it-pro/). Features of the pro version include:

* Show Love It links automatically
* Track your most popular posts, pages, and custom post types
* Setup in less than 5 minutes
* Enabled / disable for every registered post type
* Customize the text displayed for links and "already loved" messages
* Users can only love posts/pages once
* Works for logged-in and logged-out users
* Most Loved widget included
* No coding necessary
* Custom CSS option for advanced users
* Embed help documentation in plugin settings
* Easy to use template tags for developers

== Frequently Asked Questions ==

= Can non-logged-in users Love items? =

No, not at this time. The ability for non-logged-in users to Love items with JS cookies will be added to the [pro](https://pippinsplugins.com/love-it-pro/) version soon.

= Can I enabled the Love It link on only some post types? =

In the [pro](https://pippinsplugins.com/love-it-pro/) version you can.

== Installation ==

1. Upload love-it to wp-content/plugins
2. Click "Activate" in the WordPress plugins menu
3. A "Love It" link will be automatically added to the bottom of all posts / pages
4. Love It links are displayed to logged-in users only.

== Changelog ==

= 1.0.5 =

* Fix: PHP 4 style constructors
* Fix: Undefined indexes when first adding widget to sidebar

= 1.0.4 =

* Tested for compatibility with WordPress 4.0
* Added missing esc_attr() calls

= 1.0.3 = 

* Added new li_display_love_links_on filter that can be used to modify the post types the Love It links display on
* Load the text domain on "init"

= 1.0.1 = 

* Added the li_love_link($love_text = null, $loved_text = null) function
* Made it so the li_love_link() function can be used on index / archive pages
* Updated to JS to work with archive views

= 1.0 =

* Initial release

== Upgrade Notice ==

= 1.0.1 = 

* Added the li_love_link($love_text = null, $loved_text = null) function
* Made it so the li_love_link() function can be used on index / archive pages
* Updated to JS to work with archive views

= 1.0 =

This is the first release.
