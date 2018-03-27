<?php
/**
 * Template Name: Agencies | Map
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */


require_once( get_template_directory() . '/classes/EqarApi.class.php' );

$eqarApi = new EqarApi();
$context = Timber::get_context();

$context['countries'] = $eqarApi->getCountries();
$context['post'] = new TimberPost();

Timber::render('agency-map.twig', $context);
