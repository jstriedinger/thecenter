<?php
/*
Template Name: otro
*/
$context = Timber::get_context();
$context['post'] = new Timber\Post();
$templates = array( 'programa.twig' );


Timber::render( $templates, $context );