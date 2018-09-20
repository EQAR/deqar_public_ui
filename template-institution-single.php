<?php
/**
 * Template Name: Institution | Single
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

// Check if the agency is set.
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    // Get the agency ID from the GET variable.
    $institutionId  = $_GET['id'];
    $institution    = $eqarApi->getInstitution( $institutionId );

    if ( isset($institution) && !empty($institution) && $institution != false ) {

        $context['institution'] = $institution;

        $levels = '';
        foreach( array_reverse($institution->qf_ehea_levels) as $level ){
            $levels .= ucwords($level->qf_ehea_level) . ', ';
        }

        $levels = rtrim($levels,", ");
        $context['institution']->levels = $levels;

    } else {
        Site::do404($context);
    }

} else {
    Site::do404($context);
}

// Render the twig template.
Timber::render('institution-single.twig', $context);
