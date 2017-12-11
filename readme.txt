=== Plugin Name ===
Contributors: yehiasedkydev
Donate link: https://blockchain.info/address/1NEqxPfd8DaJSrkrh4jrJrHKSkhcecqRtS
Tags: widget, arabic, RSS, News, aljazeera, aljazeera.net
Requires at least: 3.2
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fetches Arabic news RSS feed from aljazeera.net and displays it in a widget

== Description ==

A simple widget plugin that fetches the latest Arabic RSS news from [aljazeera.net](http://aljazeera.net). The plugin creates a widget to place in any "widget area" on your site. It has a variable width and height and should be very flexible with your layout.

Features:

* Choose number of news items to show
* Show or hide the description of each news item
* Show or hide Aljazeera logo and link
* News is cached for 10 minutes to decrease the load on your web server

**Note**: This is an *UNOFFICIAL* plugin and there is no relation between the developer and Aljazeera news channel

If you want to change the widget style, add your style to the "custom.css" file in the plugin directory "/wp-content/plugins/aljazeera-rss/".

As from May 2017, aljazeera.net website is blocked in some Arab countries like Egypt and UAE. If your web server is located in one of these countries, the plugin won't be able to fetch the news. You may look for a workaround like VPN for example.

Github for this plugin: [Aljazeers RSS](http://github.com/ahmedessamdev/Aljazeera-RSS)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/aljazeera-rss` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to "Appearance->widgets" and you should find "Aljazeera RSS" there
4. Drag it to any "widget area" on the right and set the options

== Frequently Asked Questions ==

= Is there an English version for Aljazeera English? =

Currently, there is only an Arabic version

= How do I change the history format or time zone? =

The history format is taken from wordpress. If you change the wordpress history format setting it will change accordingly. The timezone is GMT taken from the RSS feed.

= How do I change the colors of the widget? =

You will have to do this manually, put your CSS rules in the "custom.css" file located in the plugin folder "/wp-content/plugins/aljazeera-rss/" .This file is loaded after the plugin style so it will overwrite the default style

== Screenshots ==

1. The widget with 5 items and no description
2. The widget with 3 items, description and without Aljazeera logo
3. The widget in admin area

== Changelog ==

= 1.2 =
* Fixed the plugin info and the upgrade notice bug
* Fixed a bug in the previous version prevented Aljazeera Logo from appearing

= 1.1 =
* Updated the code to work with WordPress 4.4.2

= 1.0 =
* First public release

== Upgrade Notice ==

= 1.2 =
Fixed the plugin info and the upgrade notice
