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
$showList = false;

if ( isset($_GET['list']) && !empty( $_GET['list'] ) && $_GET['list']  == 'true' ) {
    $showList = true;
}

// Countries with agencies map (default map)
if ( is_page('87') || is_page('420') ) {
    $context['maptype']   = 'agencies';
    $context['maptype_list'] = $showList;

    if ( is_page('87') ) {
        $context['agencies']  = $eqarApi->getAgencies();
    }

    Timber::render('register-map-page.twig', $context);
}

// $limit = 999, $offset = 0, $external_qaa = false, $european_approach = false, $eqar_governmental_member = false

// Map Cross-Border Recognition
elseif ( is_page('426') || is_page('1625') ) {
    $context['countries'] = $eqarApi->getCountries( 999, 0, $external_qaa = true );
    $context['maptype']   = 'cross-border';
    $context['maptype_list'] = $showList;
    Timber::render('register-map-page.twig', $context);
}

// Map European Approach for QA of JP
elseif ( is_page('428') ) {
    $context['countries'] = $eqarApi->getCountries( 999, 0, false, $european_approach = true );
    $context['maptype']   = 'eaqejp';
    $context['maptype_list'] = $showList;
    Timber::render('register-map-page.twig', $context);
}

// Map Govermental members
elseif ( is_page('430') ) {
    $context['countries'] = $eqarApi->getCountries( 999, 0, false, false, $eqar_governmental_member = true );
    $context['maptype']   = 'govermental';
    $context['maptype_list'] = $showList;
    Timber::render('register-map-page.twig', $context);
}
