=== WordPress Simple Link Library  ===
Contributors: MaikelM
Tags: links, library
Stable tag: 3.8
Requires at least: 4.0
Tested up to: 4.2.2
License: GPLv3

Manage your link collection in a simple way.

== Description ==
Manage and display links on a WordPress site. Plugin uses custom types, so exporting is very easy.
To display links in a post or page use the following short tags:

[links cat="name of category"]

[SHOWLINKS] for displaying all links

Plugin has broken link check functionality.


== Installation ==
Follow this steps to install this plugin:

1. Download the plugin into the **/wp-content/plugins/** folder
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Basic output with [SHOWLINKS]  tag 
or see https://nocomplexity.com/openarchitecture/ for a live demo.

How does it work?
This module makes use of Custom Post Types with non hierarchical catelogue option. So you can give links one or multiple tags.
Tags works great for retrieving or exporting only certain types of links.
If all tags for a link are deleted, the link still exist. This is default WP functionality. Since this plugin in built on using default wordpress hooks,
exporting and importing links can be done used as with post or pages. 
