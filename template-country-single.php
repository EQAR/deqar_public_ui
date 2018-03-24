<?php
/**
 * Template Name: Country | Single
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

// Get the agency ID from the GET variable.
$countryId = $_GET['id'];

// Check if the agency is set.
if ( isset($countryId) && !empty($countryId) ) {
    $context['country'] = $eqarApi->getCountry( $countryId );
} else {
    $context['country'] = false;
}

// Render the twig template.
Timber::render('country-single.twig', $context);
