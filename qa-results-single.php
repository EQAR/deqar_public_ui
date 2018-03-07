<?php
/**
 * Template Name: QA Results | Single
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$context['post'] = new TimberPost();

Timber::render('qa-results-single.twig', $context);
