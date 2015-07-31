<?php

namespace taxonomytaxi\electric_boogaloo;

require __DIR__.'/activation.php';

/*
*
*	@param string
*	@param int|object
*	@param string
*	@param string
*/
function get_edit_term_link( $location, $term_id, $taxonomy, $object_type ){
	if( !is_object($term_id) || $term_id->term_id != 0 )
		return $location;
	
	$args = array(
		'taxonomy' => $taxonomy,
	);
	
	if( trim($object_type) )
		$args['post_type'] = $object_type;
		
	$location = add_query_arg( $args, admin_url('edit-tags.php') );
	
	return $location;
}
add_filter( 'get_edit_term_link', __NAMESPACE__.'\get_edit_term_link', 10, 4 );

/*
*
*	@param string
*	@return string
*/
function queried_taxonomy( $query = NULL ){
	static $queried_taxonomy = NULL;
	
	if( $query )
		$queried_taxonomy = $query;
	
	return $queried_taxonomy;
}

/*
*	
*	@param WP_Query
*	@return string	
*/
function parse_query( &$wp_query ){
	if( isset($wp_query->tax_query->queries[0]) ){
		queried_taxonomy( $wp_query->tax_query->queries[0]['taxonomy'] );
	}
	
	return $wp_query;
}
add_filter( 'parse_query', __NAMESPACE__.'\parse_query' );

/*
*
*/
function posts_request( $sql, $wp_query ){
	//dbug( $sql );
	return $sql;
}
add_filter( 'posts_request', __NAMESPACE__.'\posts_request', 10, 2 );

/*
*
*/
function pre_get_posts( $wp_query ){
	$queried_taxonomy = queried_taxonomy();

	if( isset($wp_query->query_vars[$queried_taxonomy]) && ($wp_query->query_vars[$queried_taxonomy] == 'show-all-terms') ){
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT $wpdb->term_taxonomy.term_id
								FROM $wpdb->term_taxonomy
								WHERE taxonomy = %s", $queried_taxonomy );

		$slugs = $wpdb->get_col( $sql );

		$wp_query->query_vars[$queried_taxonomy] = '';
		$wp_query->query_vars['tax_query'] = array(
			array(
				'taxonomy' => $queried_taxonomy,
				'field' => 'term_id',
				'terms' => $slugs,
			)
		);
	}

	return $wp_query;
}
add_filter( 'pre_get_posts', __NAMESPACE__.'\pre_get_posts', 10, 1 );

/*
*	filter for `rewrite_rules_array` to add taxonomy base slugs directly before last catch alls
*	calls `{slug}_taxonomytaxi-two_rewrite_rules` filters
*	@param array
*	@return array	
*/
function rewrite_rules_array( $r ){
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
		
		$taxonomy_rules = array(
			$slug.'/page/?([0-9]{1,})' => 'index.php?'.$taxonomy.'=show-all-terms&paged=$matches[1]',
			$slug => 'index.php?'.$taxonomy.'=show-all-terms'
		);
		
		$taxonomy_rules = apply_filters( $slug.'_taxonomytaxi-two_rewrite_rules', $taxonomy_rules );
		
		$new_rules = array_merge( $new_rules, $taxonomy_rules );
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
add_filter( 'rewrite_rules_array', __NAMESPACE__.'\rewrite_rules_array' );