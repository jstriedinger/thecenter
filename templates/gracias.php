<?php
/*
Template Name: gracias
*/
$context = Timber::get_context();
$context['post'] = new Timber\Post();
$templates = array( 'gracias.twig' );


Timber::render( $templates, $context );