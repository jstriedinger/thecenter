<?php
/*
Template Name: justificacion
*/
$context = Timber::get_context();
$context['post'] = new Timber\Post();
$templates = array( 'justification.twig' );


Timber::render( $templates, $context );