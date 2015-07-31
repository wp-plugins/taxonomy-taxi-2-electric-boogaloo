=== Taxonomy Taxi Two Electric Boogaloo ===
Contributors: postpostmodern
Donate link: http://www.heifer.org/
Tags: custom taxonomies, taxonomy
Requires at least: 3.1.3
Tested up to: 4.2.3
Stable tag: trunk

Allows browsing all posts of any taxonomy type.

== Description ==
Allows browsing all posts of any taxonomy type.

== Installation ==
1. Place /taxonomi-taxi-2/ directory in /wp-content/plugins/
1. Rewrite rules should be flushed automatically on activation, if not save Permalink options, or delete the `rewrite_rules` setting in your options table.
1. Where you could previously only see posts in one term at a url like http://domain/taxonomy/slug/, you can now see all posts with any term within the taxonomy at http://domain/taxonomy/ and http://domain/taxonomy/page/2/ etc. 

== Changelog ==
= 0.6 =
* Fix the quries using proper filtering

= 0.5 =
* Start on fix for wp 4.2 which fixed the exploited sql

= 0.45 =
* Fix undefined term_id when admin bar is showing

= 0.4 =
* Finish namespace refactor

= 0.3 =
* Start on namespace refactor

= 0.21 =
* Quick fix for wp_title

= 0.2 =
* Updated compatibility for latest WordPress

= 0.1 =
* First release

== Screenshots ==
1. Taxonomies are rad!
