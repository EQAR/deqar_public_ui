<?php
/**
 * Template Name: Redirect
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

if ( get_field('redirect') ) {

    wp_redirect( get_field('redirect') );
    exit;

} else {

    $context = Timber::get_context();
    $context['post'] = new TimberPost();

    Timber::render('page.twig', $context);
}