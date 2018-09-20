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

// Check if the agency is set.
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    // Get the agency ID from the GET variable.
    $countryId = $_GET['id'];
    $country = $eqarApi->getCountry( $countryId );

    if ( isset($country) && !empty($country) && $country != false ) {
        $context['country'] = $country;
    } else {
        Site::do404($context);
    }

} else {
    Site::do404($context);
}

// Render the twig template.
Timber::render('country-single.twig', $context);
