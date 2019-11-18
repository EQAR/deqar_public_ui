<?php
/**
 * Template Name: Country | Single
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
$context['pages'] = array(
        'qa_results' =>         get_field('qa-results_page'),
        'agency' =>             get_field('agency_page'),
        'members' =>            get_field('members_page'),
        'cross_border' =>       get_field('cross-border_page'),
        'european_approach' =>  get_field('european-approach_page'),
        'country' =>            get_field('country_page') ?: $context['post']->link ,
    );

// Check if the agency is set.
if ( isset($_GET['id']) && !empty($_GET['id']) ) {

    // Get the agency ID from the GET variable.
    $countryId = $_GET['id'];
    $country = $eqarApi->getCountry( $countryId );

    if ( isset($country) && !empty($country) && $country != false ) {
        $context['country'] = $country;
    } else {
        Site::do404();
    }

    $allCountries = $eqarApi->getCountries();
    $context['countriesAll'] = $allCountries;

} else {
    Site::do404(400, "Country ID must be provided.");
}

// Render the twig template.
Timber::render('country-single.twig', $context);
