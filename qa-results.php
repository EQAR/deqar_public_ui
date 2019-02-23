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
$context['countries']               = $eqarApi->getCountriesByReports();
$context['agencies']                = $eqarApi->getAgencies();
$context['pages'] = array(
        'institution' =>        get_field('institution_page'),
        'country' =>            get_field('country_page'),
        'agency' =>             get_field('agency_page'),
        'qa_results' =>         get_field('qa-results_page') ?: $context['post']->link,
        'faq' =>                get_field('faq_page'),
    );

/**
 * Get all Institutions
 * @param  boolean $limit                           Number of entries to display.
 * @param  boolean $offset                          Number of entries to skip/offset.
 * @param  string  $ordering                        Define the field upon which the results should be ordered
 * @param  string  $query                           Search string to search in institution names, countries and cities.
 * @param  integer $agency                          ID of an agency. The resultset contains institutions, about which agencies were submitting reports.
 * @param  integer $country                         ID of a country. The resultset contains institutions located in the selected countries or a programme (from a report) was listed in the submitted country
 * @param  integer $qf_ehea_level                   ID of a QF EHEA Level record. The resultset contains institutions where the QF EHEA level were set to the submitted value. Values are: 1 - short cycle, 2 - first cycle, 3 - second cycle, 4 - third cycle
 * @param  integer $status                          ID of the Report Status record. The resultset contains institutions where the connecting reports were submitted with the value. Values are: 1 - part of obligatory EQA system, 2 - voluntary
 * @param  integer $report_year                     Year of the report. The resultset contains institutions where the connecting reports are valid in the submitted year
 * @param  boolean $focus_country_is_crossborder    The resultset contains institutions where one of the focus countries are set as cross boarder country
 * @param  boolean $history                         Indicator if the search should go trhough historical data.
 * @return array                                    Array of institutions
 */


if ( isset( $_GET ) && !empty( $_GET ) ) {

    $limit          = 20;
    $offset         = 0;
    $ordering       = 'DESC';
    $page           = 0;
    $query          = '';
    $agency         = '';
    $esg_activity   = '';
    $country        = '';
    $qf_ehea_level  = '';
    $status         = '';
    $report_year    = '';
    $focus_country_is_crossborder = '';
    $history        = 'true';

    if ( !empty($_GET['limit']) ) {
        $limit = $_GET['limit'];
    }
    if ( !empty($_GET['offset']) ) {
        $offset = $_GET['offset'];
    }
    if ( !empty($_GET['paging']) ) {
        $page = $_GET['paging'];
    }
    if ( !empty($_GET['ordering']) ) {
        $ordering = $_GET['ordering'];
    }
    if ( !empty($_GET['query']) ) {
        $query = $_GET['query'];
    }
    if ( !empty($_GET['agency']) ) {
        $agency = $_GET['agency'];
    }
    if ( !empty($_GET['esg_activity']) ) {
        $esg_activity = $_GET['esg_activity'];
    }
    if ( !empty($_GET['country']) ) {
        $country = $_GET['country'];
    }
    if ( !empty($_GET['qf_ehea_level']) ) {
        $qf_ehea_level = $_GET['qf_ehea_level'];
    }
    if ( !empty($_GET['status']) ) {
        $status = $_GET['status'];
    }
    if ( !empty($_GET['report_year']) ) {
        $report_year = $_GET['report_year'];
    }
    if ( !empty($_GET['focus_country_is_crossborder']) ) {
        $focus_country_is_crossborder = $_GET['focus_country_is_crossborder'];
    }
    // if ( !empty($_GET['history']) ) {
    //     $history = $_GET['history'];
    // }



    $offset  = $limit * $page;

    $results = $eqarApi->getInstitutions( $limit, $offset, $ordering, $query, $agency, $esg_activity, $country, $qf_ehea_level, $status, $report_year, $focus_country_is_crossborder, $history );

    $total   = intval($results->count);
    $skip    = $offset;
    $paged   = $results->results;
    $pages   = intval( ceil($total / $limit) );
    $current = ($page + 1);

    $details = [
        'total'     => $total,
        'perPage'   => $limit,
        'pages'     => $pages,
        'start'     => ($skip + 1),
        'end'       => ($skip + count($paged)),
        'first'     => ($current == 1 ? true : false),
        'last'      => ($pages == $current ? true : false),
        'current'   => $current,
        'prev'      => ($page == 0 ? false : ($current - 1)),
        'next'      => ($pages == ($page + 1) ? false : ($current + 1)),
    ];

    $context['results']     = $results;
    $context['formdata']    = $_GET;
    $context['paginator']   = $details;

    Timber::render('qa-results.twig', $context);

} else {

    Timber::render('qa-results-home.twig', $context);

}


