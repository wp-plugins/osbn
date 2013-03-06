=== OSBN ===
Contributors: simpson-fan
Requires at least: 3.3
Tested up to: 3.5.1
Version: 1.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WordPress plugin for the "Open-Source-Blog-Netzwerk" (osbn.de)


== Description ==

This is the WordPress plugin for the "Open-Source-Blog-Netzwerk"(osbn.de). It brings a sidebar widget with some general information about the OSBN and a list of actual articles. 

Further it removes the "nofollow" link attribute from comment author links, for OSBN member comment authors.

This functions can be activated separately and configured.


== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates


== Frequently asked questions ==

= How often the plugin calls back to the osbn.de server? =

The plugin calls back to the osbn.de server hourly and loads the last articlelist and the list of blog members from it.


== Changelog ==

1.0.0 | 27.02.2013 - Initial release
1.0.1 | 27.02.2013 - Fix dead image link
1.1.0 | 28.02.2013 - New setting options (number of articles, scrollbar/fixed height)
                     Last updated time removed
                     Fixed bug with osbn.de-link when logo disabled
1.1.1 | 02.03.2013 - Readme updated
