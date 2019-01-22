<?php
/**
 * Template Name: Agencies | List
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
$context['agencies'] = $eqarApi->getAgencies();
$context['pages'] = array(
        'agency' =>         get_field('agency_page'),
        'qa_results' =>     get_field('qa-results_page'),
    );

Timber::render('agency-list.twig', $context);

