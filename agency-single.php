<?php
/**
 * Template Name: Agencies | Single
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

include( get_template_directory() . '/classes/RestClient.class.php');

$context            = Timber::get_context();
$context['post']    = new TimberPost();

// Get the agency ID from the GET variable.
$agencyId = $_GET['id'];

// Check if the agency is set.
if ( isset($agencyId) && !empty($agencyId) ) {

    // Get the agency data
    $agency = new RestClient([
        'base_url'  => 'https://backend.deqar.eu/webapi/v1/browse/agencies/' . $agencyId . '/?history=false',
        'format'    => "json",
        'headers'   => [ 'Authorization' => 'Bearer 03a64a808705672b4fc61f0da1fe28d430250cd4' ],
    ]);

    // Get all countries of the agency
    $countries = new RestClient([
        'base_url'  => 'https://backend.deqar.eu/webapi/v1/browse/countries/by-agency-focus/' . $agencyId . '/?limit=999&offset=0',
        'format'    => "json",
        'headers'   => [ 'Authorization' => 'Bearer 03a64a808705672b4fc61f0da1fe28d430250cd4' ],
    ]);

    // Prepare the api data
    $rAgency        = $agency->get('');
    $rCountries     = $countries->get('');

    // Check if the agency exists
    if($rAgency->info->http_code == 200) {
        $context['agency'] = $rAgency->decode_response();
    } else {
        $context['agency'] = false;
    }

    // Check contries exist
    if($rCountries->info->http_code == 200) {
        $context['agency']->countries = $rCountries->decode_response()->results;
    }

} else {

    $context['agency'] = false;

}

// Render the twig template.
Timber::render('agency-single.twig', $context);
