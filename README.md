# Paascongres 2017 Content #

Content logic for the Paascongres 2017 event site

## Description ##

> This WordPress plugin requires at least [WordPress](https://wordpress.org) 4.7 and [BuddyPress](https://buddypress.org) 2.7.

This plugin contains the following object structures and features:

* Lectures/Talks (post type) + Speaker (taxonomy)
* Workshops (post type) + Round (taxonomy) + Category (taxonomy) + Speaker (taxonomy) + Location (taxonomy)
* Agenda items (post type) + Day (taxonomy) + Location (taxonomy)
* Speakers (taxonomy)
* Locations (taxonomy)
* Associations (taxonomy)
* Settings for permalink structures and enrollment logic

The plugin is built with the following (unforced) plugin requirements in mind:
* [BuddyPress](https://wordpress.org/plugins/buddypress/) for attendee profiles and enrollment
* [BP XProfile Relationship Field](https://github.com/lmoffereins/bp-xprofile-relationship-field/) for workshop selection in a profile field

The following plugins are suggested for use, depending on your configuration:
* [BP Multiblog Mode](https://github.com/lmoffereins/bp-multiblog-mode/) for using a dedicated BuddyPress configuration in your Multisite subsite
* [BP XProfile Field Read Only](https://github.com/lmoffereins/bp-xprofile-field-read-only/) for marking profile fields uneditable
* [WP Term Order](https://wordpress.org/plugins/wp-term-order/) to order taxonomy terms (associations, speakers, workshop rounds, etc.)

## Installation ##

If you download Paascongres 2017 Content manually, make sure it is uploaded to "/wp-content/plugins/paco2017-content/".

Activate Paascongres 2017 Content in the "Plugins" admin panel using the "Activate" link.

## Updates ##

This plugin is not hosted in the official WordPress repository. Instead, updating is supported through use of the [GitHub Updater](https://github.com/afragen/github-updater/) plugin by @afragen and friends.

## Contributing ##

You can contribute to the development of this plugin by [opening a new issue](https://github.com/vgsr/paco2017-content/issues/) to report a bug or request a feature in the plugin's GitHub repository.
