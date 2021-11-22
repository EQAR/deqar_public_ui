<?php
/**
 * Template Name: Agencies | EQAR decisions
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
$context['decisions'] = $eqarApi->getDecisions();
$context['agenciesByName'] = $eqarApi->getAgenciesByName();
$context['pages'] = array(
        'agency' => get_field('agency_page'),
    );

Timber::render('eqar-decisions.twig', $context);

