<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */
$context = Timber::get_context();
$context['posts'] = new Timber\PostQuery();
$context['post'] = new Timber\Post();
$context["categories"] = get_categories();
if ( is_front_page() ) {
	$templates = array( 'frontpage.twig' );
	//find all events
	$args = array (
	    'post_type'              => 'page',
	    'posts_per_page' => '1'
	);
	$args['meta_query'] =  array(array('key' => '_wp_page_template','compare' => '==','value' => 'templates/lideres.php'));
	$context['programa'] = Timber::get_posts( $args )[0];
}
else{
	$templates = array( 'normal.twig' );
}


Timber::render( $templates, $context );

