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


// Registered agencies - agency based in - /browse/countries/
// Cross-border recognition - external_QAA_is_permitted = true - /browse/countries/?external_qaa=true
// European approach for QA of JP - european_approach_is_permitted = true - /browse/countries/?european_approach=true
// Govermental members - eqar_governmental_member_start filled in - /browse/countries/?eqar_governmental_member=true

// "european_approach_is_permitted": "no",
// "external_QAA_is_permitted": "no",
// "eqar_governmental_member_start": null,

$allCountries = $eqarApi->getCountries();
$context['countriesAll'] = $allCountries;

// Map Cross-Border Recognition
if ( is_page('426') || is_page('1625') ) {
//    $context['countries'] = $eqarApi->getCountries( 999, 0, $external_qaa = true );
    $context['maptype']   = 'cross-border';
}

// Map European Approach for QA of JP
elseif ( is_page('428') ) {
//    $context['countries'] = $eqarApi->getCountries( 999, 0, false, $european_approach = true );
    $context['maptype']   = 'eaqejp';
}

// Map Govermental members
elseif ( is_page('430') ) {
//    $context['countries'] = $eqarApi->getCountries( 999, 0, false, false, $eqar_governmental_member = true );
    $context['maptype']   = 'govermental';
}

// blank map as default
else {
    $context['maptype']   = 'blank';
}

Timber::render('register-map-page.twig', $context);

