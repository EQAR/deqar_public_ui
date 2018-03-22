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

include( get_template_directory() . '/classes/RestClient.class.php');

$context = Timber::get_context();
$context['post'] = new TimberPost();

$api = new RestClient([
    'base_url' => "https://backend.deqar.eu/webapi/v1/browse/agencies/?limit=999&offset=0",
    'format' => "json",
    'headers' => ['Authorization' => 'Bearer 03a64a808705672b4fc61f0da1fe28d430250cd4' ],
]);

$result = $api->get('agencies' );

if($result->info->http_code == 200) {
    $context['agencies'] = $result->decode_response();
} else {
    $context['agencies'] = [];
}

Timber::render('agency-list.twig', $context);
