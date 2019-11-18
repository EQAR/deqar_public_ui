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

// check if report ID is provided
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    $context['report'] = $eqarApi->getReport($_GET['id']);

    if ($context['report'] == false) {
        Site::do404();
    }

} else {
    Site::do404(400, "DEQAR report ID must be provided.");
}

// Render the twig template.
Timber::render('qa-result-single.twig', $context);

