<?php
/**
 * Template Name: National Implementation Map
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package     WordPress
 * @subpackage  Timber
 * @since       Timber 0.1
 */

require_once( get_template_directory() . '/classes/EqarApi.class.php' );

$eqarApi = new EqarApi();
$context = Timber::get_context();

$context['post'] = new TimberPost();

$allCountries = $eqarApi->getCountries();

$context['countriesAll'] = $allCountries;
$context['countries']    = $eqarApi->getCountries( 999, 0, false, $european_approach = true );
$context['maptype']      = 'eaqejp';

Timber::render('register-map-page.twig', $context);
