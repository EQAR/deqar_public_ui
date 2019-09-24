<?php
/**
 * Template Name: Register | Generic Map
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

$allCountries = $eqarApi->getCountries();
$context['countriesAll'] = $allCountries;
$context['map'] = array(
        'type' =>           get_field('maptype'),
        'country_page' =>   get_field('country_page'),
        'cross_border' => array(
            'yes' =>        get_field('cross-border_yes', 'option'),
            'partially' =>  get_field('cross-border_partially', 'option'),
            'no' =>         get_field('cross-border_no', 'option'),
        ),
        'european_approach' => array(
            'yes' =>        get_field('european-approach_yes', 'option'),
            'partially' =>  get_field('european-approach_partially', 'option'),
            'no' =>         get_field('european-approach_no', 'option'),
        ),
        'ehea_kc' =>        get_field('ehea-kc_labels', 'option'),
    );

Timber::render('register-map-page.twig', $context);

