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
$context['institutions']            = $eqarApi->getInstitutions();
$context['countries']               = $eqarApi->getCountries();
$context['agencies']                = $eqarApi->getAgencies();
$context['institutionsByCountry']   = $eqarApi->getInstitutionsByCountry();



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


if ( isset( $_POST ) && !empty( $_POST ) ) {

    // var_dump( $_POST );

    $limit          = 999;
    $offset         = 0;
    $ordering       = 'DESC';
    $query          = false;
    $agency         = false;
    $country        = false;
    $qf_ehea_level  = false;
    $status         = false;
    $report_year    = false;
    $focus_country_is_crossborder = false;
    $history        = false;


    if ( !empty($_POST['limit']) ) {
        $limit = $_POST['limit'];
    }
    if ( !empty($_POST['offset']) ) {
        $offset = $_POST['offset'];
    }
    if ( !empty($_POST['ordering']) ) {
        $ordering = $_POST['ordering'];
    }
    if ( !empty($_POST['query']) ) {
        $query = $_POST['query'];
    }
    if ( !empty($_POST['agency']) ) {
        $agency = $_POST['agency'];
    }
    if ( !empty($_POST['country']) ) {
        $country = $_POST['country'];
    }
    if ( !empty($_POST['qf_ehea_level']) ) {
        $qf_ehea_level = $_POST['qf_ehea_level'];
    }
    if ( !empty($_POST['status']) ) {
        $status = $_POST['status'];
    }
    if ( !empty($_POST['report_year']) ) {
        $report_year = $_POST['report_year'];
    }
    if ( !empty($_POST['focus_country_is_crossborder']) ) {
        $focus_country_is_crossborder = $_POST['focus_country_is_crossborder'];
    }
    if ( !empty($_POST['history']) ) {
        $history = $_POST['history'];
    }

    $results = $eqarApi->getInstitutions( $limit, $offset, $ordering, $query, $agency, $country, $qf_ehea_level, $status, $report_year, $focus_country_is_crossborder, $history );

    $context['results'] = $results;
    $context['formdata'] = $_POST;

    // var_dump( $results );

}

Timber::render('qa-results.twig', $context);
