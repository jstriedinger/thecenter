<?php
/*
Template Name: programa
*/
$context = Timber::get_context();
$context['post'] = new Timber\Post();
$templates = array( 'lideres.twig' );


Timber::render( $templates, $context );