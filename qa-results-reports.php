<?php
/**
 * Template Name: QA Results | Search by reports
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
$context['countries']               = $eqarApi->getCountriesByReports();
foreach($context['countries'] as $country) {
    $context['countriesByID'][$country->id] = $country;
}
$context['agencies']                = $eqarApi->getAgencies();
foreach($context['agencies'] as $agency) {
    $context['agenciesByID'][$agency->id] = $agency;
}
$context['pages'] = array(
        'report' =>             get_field('report_page'),
        'institution' =>        get_field('institution_page'),
        'country' =>            get_field('country_page'),
        'agency' =>             get_field('agency_page'),
        'qa_results' =>         get_field('qa-results_page') ?: $context['post']->link,
        'faq' =>                get_field('faq_page'),
    );

$parameter_list = array(
    'limit',                                        // Number of entries to display.
    'offset',                                       // Number of entries to skip/offset.
    'offset_page',                                  // Number of entries to skip/offset as page number
    'ordering',                                     // Define the field upon which the results should be ordered
);

foreach($context['report_search']['facets'] as &$facet) {
    // add facet to list of allowed parameters
    $parameter_list[] = $facet['tag'];
    // re-organise value labels
    if ( is_array($facet['value_labels']) ) {
        $newArray = array();
        foreach($facet['value_labels'] as $label) {
            $newArray[$label['value']] = $label['label'];
        }
        $facet['value_labels'] = $newArray;
    }
}

// default parameters - currently, only for limit we have a default
$parameters = array(    'limit'     => 20,              // 20 results/page
                        'offset'    => 0,               // first page
                        'ordering'  => '-date_created', // default sort (score if query, name else)
    );

foreach ($parameter_list as $p) {
    if ( isset($context['request']->get[$p]) ) {
        $parameters[$p] = $context['request']->get[$p];
    }
}

if (isset($parameters['offset_page'])) {
    $parameters['offset'] = ( $parameters['offset_page'] - 1 ) * $parameters['limit'];
    unset($parameters['offset_page']);
}

if (isset($parameters['agency']) && is_numeric($parameters['agency'])) {
    $parameters['agency'] = $context['agenciesByID'][$parameters['agency']]->acronym_primary;
}

if (isset($parameters['country']) && is_numeric($parameters['country'])) {
    $parameters['country'] = $context['countriesByID'][$parameters['country']]->name_english;
}

$results = $eqarApi->getReports($parameters);

$pages = intval( ceil($results->count / $parameters['limit']) );

$context['paginator'] = array(
    'pages'     => $pages,
    'limit'     => $parameters['limit'],
    'current'   => $parameters['offset'] / $parameters['limit'] + 1,
    'prev'      => ($parameters['offset'] == 0 ? null : $parameters['offset'] - $parameters['limit'] ),
    'next'      => ($parameters['offset'] >= ($pages - 1) * $parameters['limit'] ? null : $parameters['offset'] + $parameters['limit'] ),
    'total'     => $results->count,
    'start'     => ($parameters['offset'] + 1),
    'end'       => ($parameters['offset'] + count($results->results)),
);

$context['results']     = $results;
$context['formdata']    = $parameters;

Timber::render('qa-results-reports.twig', $context);

