<?
/*
Plugin Name: Taxonomy Taxi 2 : Electric Boogaloo
Plugin URI: 
Description: 
Version: .2
Author: Eric Eaglstun
Author URI: 
*/

class TaxoTaxi2ElecBoog{
	private static $queried_taxonomy = '';
	
	/*
	*	called on `init` action to attach the proper filters 
	*/
	public static function setup(){
		add_filter( 'parse_query', 'TaxoTaxi2ElecBoog::parse_query' );
		add_filter( 'rewrite_rules_array', 'TaxoTaxi2ElecBoog::rewrite_rules_array' );
		add_filter( 'sanitize_title', 'TaxoTaxi2ElecBoog::sanitize_title', 10, 3 );	
	}
	
	/*
	*	called on activation hook
	*/
	public static function activate(){
		self::setup();
		flush_rewrite_rules( FALSE );
	}
	
	/*
	*	called on deactivation hook
	*/
	public static function deactivate(){
		flush_rewrite_rules( FALSE );
	}
	
	/*
	*	attached to 
	*	@param WP_Query
	*	@return string	
	*/
	public static function parse_query( &$wp_query ){
		if( isset($wp_query->tax_query->queries[0]) )
			self::$queried_taxonomy = $wp_query->tax_query->queries[0]['taxonomy'];
			
		return $wp_query;
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
			
			$new_rules[ $slug.'/page/?([0-9]{1,})' ] = 'index.php?'.$taxonomy.'=show-all-terms&paged=$matches[1]';
			$new_rules[ $slug ] = 'index.php?'.$taxonomy.'=show-all-terms';
		}
		
		// insert new rewrite rules directly before the catch alls
		$k = array_keys( $r );
		$p = array_search( '(.+?)/page/?([0-9]{1,})/?$', $k );
		
		// @TODO figure out a better way of finding this
		if( !$p )
			$p = array_search( 'page/?([0-9]{1,})/?$', $k );
			
		$a = array_slice( $r, 0, $p );
		$b = array_slice( $r, $p );
		
		$r = array_merge( $a, $new_rules, $b );
			
		return $r;
	}
	
	/*
	*	filter for `sanitize_title`. this is the awful hack where the magic happens.
	*	check out wp-includes/taxonomy.php/transform_query() under case 'slug': to see the sql this affects
	*	@param string
	*	@param string
	*	@param string
	*	@return string
	*/
	public static function sanitize_title( $title, $raw_title, $context ){
		// @TODO: allow user to customize this slug, and avoid any name conflicts with terms
		if( $raw_title != 'show-all-terms' )
			return $title;
		
		// @TODO: make sure the other contexts work
		switch( $context ){
			case 'query':
				$title = "') OR (taxonomy = '".self::$queried_taxonomy;
				break;
			case 'save':
				global $wp_query;
				$wp_query->queried_object = (object) array( 'slug' => $title, 'taxonomy' => '' );
				break;
		}
		
		return $title;
	}
}

add_action( 'init', 'TaxoTaxi2ElecBoog::setup' );
register_activation_hook( __FILE__, 'TaxoTaxi2ElecBoog::activate' );
register_deactivation_hook( __FILE__, 'TaxoTaxi2ElecBoog::deactivate' );