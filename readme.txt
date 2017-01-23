=== Paascongres 2017 Content ===
Contributors: offereins
Tags: vgsr, paascongres, paco, conference
Requires at least: 4.7
Tested up to: 4.7
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Content logic for the Paascongres 2017 event site.

== Description ==

This plugin contains the following object structures and features:

* Lectures/Talks (post type) + Speaker (taxonomy)
* Workshops (post type) + Categories (taxonomy) + Speaker (taxonomy) Locations (taxonomy)
* Agenda items (post type) + Days (taxonomy) + Locations (taxonomy)
* Speakers (taxonomy)
* Locations (taxonomy)
* Associations (taxonomy)
* Settings for permalink structures and enrollment logic

The plugin supports BuddyPress for attendee profiles and enrollment.

== Installation ==

If you download Paascongres 2017 Content manually, make sure it is uploaded to "/wp-content/plugins/paco2017-content/".

Activate Paco2017 Content in the "Plugins" admin panel using the "Activate" link.

This plugin is not hosted in the official WordPress repository. Instead, updating is supported through use of the [GitHub Updater](https://github.com/afragen/github-updater/) plugin by @afragen and friends.

== Changelog ==

= 1.1.0 =
* Added Lecture info box template
* Added Workshop Round taxonomy
* Added Workshop info box template
* Added Workshop users and user count logic, implemented in templates and REST response
* Added menu ordering for Lectures and Workshops
* Added Conference Location detail to Agenda Item'd infos
* Added term adverbial meta logic to Conference Locations
* Added Dutch translation
* BP: Added dedicated BP admin settings page
* BP: Added workshop field selection and options filtering by workshop round
* BP: Changed to allow all profile field types for assigning the user association term

= 1.0.1 =
* Fixed bug where slug settings were not correctly used affecting the setup of rewrite rules

= 1.0.0 =
* Initial release
