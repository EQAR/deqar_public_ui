<?php
/**
 * Template Name: QA Results
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

$context['post']                    = new TimberPost();
$context['institutions']            = $eqarApi->getInstitutions();
$context['countries']               = $eqarApi->getCountries();
$context['institutionsByCountry']   = $eqarApi->getInstitutionsByCountry();

Timber::render('qa-results.twig', $context);
