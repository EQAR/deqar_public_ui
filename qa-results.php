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

if ( isset($context['request']->get['query']) and preg_match('/^\s*(DEQARINST([0-9]+)|([0-9]+)|[A-Z][A-Z]([0-9]{4}))\s*$/', strtoupper($context['request']->get['query']), $matches) ) {
    // if the search term is numerical, a DEQARINST ID or an ETER ID, redirect to the institution directly
    wp_redirect( Site::addParameters(get_field('institution_page'), array(), array('id' => $matches[0])) );
}

$context['post']                    = new TimberPost();
$context['countriesEHEA']           = $eqarApi->getCountries();
$context['countries']               = $eqarApi->getCountriesByReports();
$context['countriesByID']           = $eqarApi->getCountriesByReportsByID();
$context['countriesByName']         = $eqarApi->getCountriesByReportsByName();
$context['agencies']                = $eqarApi->getAgencies();
$context['agenciesByID']            = $eqarApi->getAgenciesByID();
$context['agenciesByName']          = $eqarApi->getAgenciesByName();
$context['pages'] = array(
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
    'query',                                        // Search string
);

foreach($context['search']['facets'] as &$facet) {
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

if ( !empty( $context['request']->get ) ) {

    // default parameters - currently, only for limit we have a default
    $parameters = array(    'limit'     => 20,                                                                      // 20 results/page
                            'offset'    => 0,                                                                       // first page
                            'ordering'  => (isset($context['request']->get['query'])
                                        && !empty($context['request']->get['query']) ? '-score' : 'name_sort' ),    // default sort (score if query, name else)
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

    $results = $eqarApi->getInstitutions($parameters);

    if ($results->count > 0) {

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

    }

    $context['results']     = $results;
    $context['formdata']    = $parameters;

    Timber::render('qa-results.twig', $context);

} else {

    Timber::render('qa-results-home.twig', $context);

}


