<?php
/**
 * Template Name: Agencies | Single
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

$context['post'] = new TimberPost();


// Check if the agency is set.
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    $agency = $eqarApi->getAgency( $_GET['id'] , 'true' );

    if ( isset($agency) && !empty($agency) && $agency != false ) {

        $context['agency'] = $agency;

    } else {

        Site::do404($context);

    }

} else {

    Site::do404($context);

}

// Render the twig template.
Timber::render('agency-single.twig', $context);
