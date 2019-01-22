<?php
/**
 * Template Name: QA Result | Single
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

require_once( get_template_directory() . '/classes/EqarApi.class.php' );

$context         = Timber::get_context();
$eqarApi         = new EqarApi();
$context['post'] = new TimberPost();
$context['pages'] = array(
        'country' =>            get_field('country_page'),
        'agency' =>             get_field('agency_page'),
        'institution' =>        get_field('institution_page'),
        'report' =>             get_field('report_page') ?: $context['post']->link ,
    );

// Check if the agency is set.
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    $context['report'] = $eqarApi->getReport($_GET['id']);

    if ($context['report'] == false) {
        Site::do404($context);
    }

    // Get the agency ID from the GET variable.
    //$institutionId  = $_GET['id'];
    //$institution    = $eqarApi->getInstitution( $institutionId );

    /*if ( isset($institution) && !empty($institution) && $institution != false ) {

        $context['institution'] = $institution;
        $context['formdata']['agency'] = isset($_GET['agency']) ? $_GET['agency'] : null;
        $context['formdata']['esg_activity'] = isset($_GET['esg_activity']) ? $_GET['esg_activity'] : null;

    } else {
        Site::do404($context);
    }*/

} else {
    Site::do404($context);
}

// Render the twig template.
Timber::render('qa-result-single.twig', $context);

