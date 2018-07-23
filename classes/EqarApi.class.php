<?php

require_once( get_template_directory() . '/classes/RestClient.class.php');

class EqarApi {


    /**
     * Base url of the API
     * @var string
     */
    const EQARBASEURL = 'https://backend.deqar.eu/webapi/v1/browse/';

    /**
     * Return format of the API
     * @var string
     */
    const EQARFORMAT  = 'json';

    /**
     * Construct the main rest client
     * @param   string      $path   Api query after the base path
     * @return  RestClient          The RestClient return object
     */
    public function eqar( $path = false ) {
        if (!defined('EQARAUTHKEY')) {
            throw new \Exception('Missing EQARAUTHKEY in wp-config.php');
        }
        if ( empty($path) ) {
            return false;
        }

        $api = new RestClient([
            'base_url'  => self::EQARBASEURL . $path,
            'format'    => self::EQARFORMAT,
            'headers'   => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . constant('EQARAUTHKEY'),
            ],
        ]);

        return $api;

    }


    /**
     * Get an Institution
     * @param   int      Institution Id
     * @param   boolean  Show historical data
     * @return  object   Institution object
     */
    public function getInstitution( $institutionId = null, $history = false )
    {

        if ( isset($institutionId) && !empty($institutionId) ) {

            $reports                = $this->getReportInstitutionalByInstitution( $institutionId );
            $reportsHistorical      = $this->getReportInstitutionalByInstitution( $institutionId, true );

            $programmes             = $this->getReportProbrammesByInstitution( $institutionId );
            $programmesHistorical   = $this->getReportProbrammesByInstitution( $institutionId, true );

            $path       = 'institutions/' . $institutionId . '/?history=' . $history;
            $api        = $this->eqar( $path );
            $result     = $api->get('');

            if($result->info->http_code == 200) {

                $output = $result->decode_response();

                $output->reports              = $reports;
                $output->reportsHistorical    = $reportsHistorical;
                $output->programmes           = $programmes;
                $output->programmesHistorical = $programmesHistorical;

                return $output;

            }

        }

        return false;

    }



    /**
     * Get all Institutions
     * @param  boolean $limit                           Number of entries to display.
     * @param  boolean $offset                          Number of entries to skip/offset.
     * @param  string  $ordering                        Define the field upon which the results should be ordered
     * @param  string  $query                           Search string to search in institution names, countries and cities.
     * @param  integer $agency                          ID of an agency. The resultset contains institutions, about which agencies were submitting reports.
     * @param  string  $esg_activity                           Search string to search in institution names, countries and cities.
     * @param  integer $country                         ID of a country. The resultset contains institutions located in the selected countries or a programme (from a report) was listed in the submitted country
     * @param  integer $qf_ehea_level                   ID of a QF EHEA Level record. The resultset contains institutions where the QF EHEA level were set to the submitted value. Values are: 1 - short cycle, 2 - first cycle, 3 - second cycle, 4 - third cycle
     * @param  integer $status                          ID of the Report Status record. The resultset contains institutions where the connecting reports were submitted with the value. Values are: 1 - part of obligatory EQA system, 2 - voluntary
     * @param  integer $report_year                     Year of the report. The resultset contains institutions where the connecting reports are valid in the submitted year
     * @param  boolean $focus_country_is_crossborder    The resultset contains institutions where one of the focus countries are set as cross boarder country
     * @param  boolean $history                         Indicator if the search should go trhough historical data.
     * @return array                                    Array of institutions
     */
    public function getInstitutions( $limit = 999, $offset = 0, $ordering = 'DESC', $query = false, $agency = false, $esg_activity = false, $country = false, $qf_ehea_level = false, $status = false, $report_year = false, $focus_country_is_crossborder = false, $history = false )
    {

        $path   = 'institutions/?limit=' . $limit . '&offset=' . $offset .'&ordering=' . $ordering .'&query=' . $query . '&agency=' . $agency . '&esg_activity=' . $esg_activity . '&country=' . $country . '&qf_ehea_level=' . $qf_ehea_level . '&status=' . $status . /*'&report_year=' . $report_year . -- emergency */ '&focus_country_is_crossborder=' . $focus_country_is_crossborder . '&foo=bar'; // CT - added junk parameter to dump .json added

        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get institution Reports
     * @param   int      $institutionId     Institution Id
     * @param   boolean  $history           Historical data parameter
     * @return  array    Reports
     */
    public function getReportInstitutionalByInstitution( $institutionId = null, $history = false )
    {

        $path   = 'reports/institutional/by-institution/' . $institutionId . '/?limit=200&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get institution Programmes
     * @param   int      $institutionId     Institution Id
     * @param   boolean  $history           Historical data parameter
     * @return  array    Programmes
     */
    public function getReportProbrammesByInstitution( $institutionId = null, $history = false )
    {

        $path   = 'reports/programme/by-institution/' . $institutionId . '/?limit=200&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get an Agency
     * @param   int      $agencyId      Agency Id
     * @param   boolean  $agencyId      Historical data
     * @return  array    All Agencies
     */
    public function getAgency( $agencyId = null, $history = false )
    {

        if ( isset($agencyId) && !empty($agencyId) ) {

            $path       = 'agencies/' . $agencyId . '/?history=' . $history;
            $api        = $this->eqar( $path );
            $result     = $api->get('');
            $countries  = $this->getAgencyCountries( $agencyId, 'true' ); //CT added quotes

            if($result->info->http_code == 200) {
                $output = $result->decode_response();
                $output->countries = $countries;
                return $output;
            }

        }

        return false;

    }


    /**
     * Get all Agencies
     * @return  array    All Agencies
     */
    public function getAgencies()
    {

        $path   = 'agencies/?limit=999&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Agencies for a Country
     * @param   int      $countryId     Country Id
     * @param   boolean  $history       Show historical data
     * @return  array                   All Agencies of a certain country
     */
    public function getAgenciesByCountry( $countryId = null, $history = false )
    {

        $path   = 'agencies/based-in/' . $countryId . '/?limit=999&offset=0&history=' . $history;
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Agencies for a Focus Country
     * @param   int      $countryId     Country Id
     * @param   boolean  $history       Show historical data
     * @return  array                   All Agencies of a certain country
     */
    public function getAgenciesByFocusCountry( $countryId = null, $history = false )
    {

        $path   = 'agencies/focusing-to/' . $countryId . '/?limit=999&offset=0&history=' . $history;
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get a Country by ID
     * @param   int      Country Id
     * @param   boolean  Show historical data
     * @return  object   Country object
     */
    public function getCountry( $countryId, $history = true )
    {

        $byCountry              = $this->getAgenciesByCountry( $countryId );
        $byCountryHistory       = $this->getAgenciesByCountry( $countryId, true );
        $byFocusCountry         = $this->getAgenciesByFocusCountry( $countryId );
        $byFocusCountryHistory  = $this->getAgenciesByFocusCountry( $countryId, true );

        $path   = 'countries/' . $countryId . '/?history=' . $history;
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {

            $output = $result->decode_response();
            $output->agencies = [
                'byCountry' => $byCountry,
                'byCountryHistory' => $byCountryHistory,
                'byFocusCountry' => $byFocusCountry,
                'byFocusCountryHistory' => $byFocusCountryHistory,
            ];

            return $output;

        }

        return false;

    }


    /**
     * Get a list of Countries
     * @param  integer $limit                       Limit the number or returned countries
     * @param  integer $offset                      Results offset (for pagin purposes)
     * @param  boolean $external_qaa                Query External QAA
     * @param  boolean $european_approach           Query External European Approach
     * @param  boolean $eqar_governmental_member    Query Eqar governmental members
     * @return array                                All Countries
     */
    public function getCountries(
        $limit = 999,
        $offset = 0,
        $external_qaa = false,
        $european_approach = false,
        $eqar_governmental_member = false )
    {

        $path   = 'countries/?limit=' . $limit . '&offset=' . $offset . '&external_qaa=' . $external_qaa . '&european_approach=' . $european_approach . '&eqar_governmental_member=' . $eqar_governmental_member;
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response();
        }

        return false;

    }


    /**
     * Get a list of Countries
     * @return array    All Countries
     */
    public function getCountriesByReports()
    {

        $path   = 'countries/by-reports/?limit=999';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response();
        }

        return false;

    }


    /**
     * Get all Countries
     * @param   int      Agency Id
     * @param   boolean  Show historical data
     * @return  array    All Countries
     */
    public function getAgencyCountries( $agencyId = null, $history = false )
    {

        $path   = 'countries/by-agency-focus/' . $agencyId . '/?limit=999&offset=0&history=' . $history . '&foo=bar'; //CT added junk parameter
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Institutions by Counctry
     * @param   int      Country Id
     * @return  array    Array of Countries with Intstitutions
     */
    public function getInstitutionsByCountry( $countryId = false )
    {

        $countries      = $this->getCountries();
        $institutions   = $this->getInstitutions();

        $output = [];

        foreach ( $countries as $country ) {

            $countryName = $country->name_english;

            $output[$countryName] = [];

            foreach ($institutions as $institution) {
                if ($institution->countries[0]->country == $countryName) {
                    $output[$countryName][] = $institution;
                }
            }

        }

        return $output;

    }

}
