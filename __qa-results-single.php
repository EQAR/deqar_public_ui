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

require_once( get_template_directory() . '/classes/EqarApi.class.php');

$eqarApi = new EqarApi();
$context = Timber::get_context();

$context['post'] = new TimberPost();

// Get the institution ID from the GET variable.
$institutionId = $_GET['id'];


var_dump( "qa-results-single.php" );
exit;

// Check if the institution is set.
if ( isset($institutionId) && !empty($institutionId) ) {

    $context['institution'] = $eqarApi->getInstitution($institutionId);

    $levels = '';
    foreach( array_reverse($eqarApi->getInstitution($institutionId)->qf_ehea_levels) as $level ){
        $levels .= ucwords($level->qf_ehea_level) . ', ';
    }

    $levels = rtrim($levels,", ");
    $context['institution']->levels = $levels;

} else {
    $context['institution'] = false;
}

// Render the twig template.
Timber::render('qa-results-single.twig', $context);
