<?php
/*
Plugin Name: Taxonomy Taxi 2 : Electric Boogaloo
Plugin URI: 
Description: 
Version: .1
Author: Eric Eaglstun
Author URI: 
*/

class TaxoTaxi2ElecBoog{
	/*
	*	called on `init` action to attach the proper filters 
	*	@todo flush rewrite rules on plugin activation/deactivation
	*/
	public static function setup(){
		add_filter( 'sanitize_title', 'TaxoTaxi2ElecBoog::sanitize_title', 10, 3 );	
		add_filter( 'posts_request', 'TaxoTaxi2ElecBoog::posts_request' );
		add_filter( 'rewrite_rules_array', 'TaxoTaxi2ElecBoog::rewrite_rules_array' );
	}
	
	/*
	*	filter for `sanitize_title`. this is the awful hack where the magic happens.
	*	uncomment out the dbug in posts_request to see the sql injection going on here
	*	@param string
	*	@param string
	*	@param string
	*	@return string
	*/
	public static function sanitize_title( $title, $raw_title, $context ){
		// TODO: allow user to customize this slug, and avoid any name conflicts with terms
		if( $raw_title != 'show-all' )
			return $title;
		
		// TODO: make sure the other contexts work
		switch( $context ){
			case 'query':
				$title = "') OR (1 = '1";
				break;
		}
		
		return $title;
	}
	
	/*
	*	filter for `posts_request`, only used for debugging
	*	@param string
	*	@return string	
	*/
	public static function posts_request( $sql ){
		//dbug( $sql );
		return $sql;
	}
	
	/*
	*	filter for `rewrite_rules_array` to add taxonomy base slugs directly before last catch alls
	*	@param array
	*	@return array	
	*/
	public static function rewrite_rules_array( $r ){
		$new_rules = array();
		
		$taxonomies = get_taxonomies( '', 'objects' );
		
		// dont duplicate defaults
		unset( $taxonomies['category'] );
		unset( $taxonomies['post_tag'] );
		unset( $taxonomies['post_format'] );
		
		foreach( $taxonomies as $taxonomy => $properties ){
			if( !$properties->rewrite )
				continue;
				
			$slug = $properties->rewrite['slug'];
			
			$new_rules[ $slug.'/page/?([0-9]{1,})' ] = 'index.php?'.$taxonomy.'=show-all&paged=$matches[1]';
			$new_rules[ $slug ] = 'index.php?'.$taxonomy.'=show-all';
		}
		
		// insert new rewrite rules directly before the catch alls
		$k = array_keys( $r );
		$p = array_search( '(.+?)/page/?([0-9]{1,})/?$', $k );
		
		$a = array_slice( $r, 0, $p );
		$b = array_slice( $r, $p );
		
		return array_merge( $a, $new_rules, $b );
	}
}

add_action( 'init', 'TaxoTaxi2ElecBoog::setup' );
