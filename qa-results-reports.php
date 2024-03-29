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

// build list of allowed parameters
$parameter_list = array(
    'limit',                                        // Number of entries to display.
    'offset',                                       // Number of entries to skip/offset.
    'offset_page',                                  // Number of entries to skip/offset as page number
    'ordering',                                     // Define the field upon which the results should be ordered
    'query'
);

foreach($context['report_search']['facets'] as &$facet) {
    // add facets to list of allowed parameters
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

// default parameters - fixed and variable ones
$parameters = array(    'limit'     => get_field('limit'),      // results/page
                        'offset'    => 0,                       // first page
                        'ordering'  => get_field('ordering'),   // default sort order
    );

if (is_array(get_field('defaults'))) {
    foreach (get_field('defaults') as $d) {
        $parameters[$d['key']] = $d['value'];
    }
}

// get actual parameters passed in URL
foreach ($parameter_list as $p) {
    if ( isset($context['request']->get[$p]) ) {
        $parameters[$p] = stripslashes($context['request']->get[$p]);
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

// finally, masked parameters always override
$context['masks'] = array();
if (is_array(get_field('masks'))) {
    foreach (get_field('masks') as $m) {
        $parameters[$m['key']] = $m['value'];
        $context['masks'][$m['key']] = $m['value'];
    }
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

