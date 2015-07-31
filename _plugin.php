<?php
/*
Plugin Name:	Taxonomy Taxi 2 : Electric Boogaloo
Plugin URI:		
Description:	
Version:		.5
Author:			postpostmodern, pinecone-dot-io
Author URI: 
*/

register_activation_hook( __FILE__, create_function("", '$ver = "5.3"; if( version_compare(phpversion(), $ver, "<") ) die( "This plugin requires PHP version $ver or greater be installed." );') );

register_activation_hook( __FILE__, '\taxonomytaxi\electric_boogaloo\activate' );
register_deactivation_hook( __FILE__, '\taxonomytaxi\electric_boogaloo\deactivate' );

require __DIR__.'/index.php';
