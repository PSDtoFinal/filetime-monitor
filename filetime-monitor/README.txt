=== Plugin Name ===
Contributors: PSDtoFinal
Tags: wp all import, importing, file monitoring, last modified
Requires at least: 4.0
Tested up to: 4.7.2
Stable tag: 4.3
License: GPLv2 or later
Version: 1.0.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Running regular imports? This plugin allows you to quickly see when your import / export or other important files were updated!

== Description ==

This plugin allows you to monitor the "Last Modified Time" of up to 20 files, at a glance - right from the WordPress Dashboard.

If you are running regular imports (through [WP All Import](https://wordpress.org/plugins/wp-all-import), [WordPress Importer](https://en-gb.wordpress.org/plugins/wordpress-importer/) or similar), this handy tool lets you check on how update your source files are; and is great for diagnosing `cron` timing mismatches.

== Installation ==

1. Upload `filetime-monitor` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to Settings => File Time Monitor to set which files to monitor

== Frequently Asked Questions ==

= Can this work on external files? =

If they are on the same physical / virtual server as your WordPress installation, yes.

= Can I monitor files over HTTP / HTTPS? =

No. PHP is NOT able to access / ascertain what time a file has been modified over HTTP(S)

= Can I monitor more than 20 files? =

Not in this version of the plugin. We're working on an update that will allow virtually unlimited files though (stay tuned).

= Can I use relative paths =

Yes, but it's not as reliable. We recommend absolute paths.

= How do I find an absolute path? =

Typically you can work this out by logging in to your website's **Server Control Panel** (cPanel, Parallels, Odin, etc), and working through the file system, there.

= I have an idea to improve this plugin =

Awesome, please hit us up on [Twitter](https://twitter.com/psdtofinal), [Facebook](https://www.facebook.com/psdtofinal/), or [our official website](https://www.psdtofinal.com/).

== Screenshots ==

1. Dashboard widget showing the times that monitored files have been updated
2. Settings screen, where you note which files are to be monitored

== Changelog ==

= 1.0 =
* Initial build and release