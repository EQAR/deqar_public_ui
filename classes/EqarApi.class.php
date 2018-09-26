<?php

require_once(get_template_directory() . '/classes/RestClient.class.php');

class EqarApi
{


    /**
     * EQAR API Base URL
     * @var string      The base url of the EQAR API
     */
    public $apiUrl;


    /**
     * EQAR API Key
     * @var string      The bearer key of the EQAR API
     */
    public $apiKey;


    /**
     * Main constructor function
     */
    public function __construct(){

        if ( !defined('EQAR_ENV') ) {

            throw new \Exception('Missing EQAR_ENV in wp-config.php. Set EQAR_ENV to either "LIVE" or "TEST"');
            return false;

        } else {

            if ( EQAR_ENV == 'TEST' ) {

                if ( !defined('EQARBASEURL_TEST') ) {
                    throw new \Exception('Missing EQARBASEURL_TEST in wp-config.php');
                    return false;
                } else {
                    $this->setApiUrl( EQARBASEURL_TEST );
                }

                if ( !defined('EQARBASEURL_TEST') ) {
                    throw new \Exception('Missing EQARBASEURL_TEST in wp-config.php');
                    return false;
                } else {
                    $this->setApiKey( EQARAUTHKEY_TEST );
                }

            }

            if ( EQAR_ENV == 'LIVE' ) {

                if ( !defined('EQARBASEURL_LIVE') ) {
                    throw new \Exception('Missing EQARBASEURL_LIVE in wp-config.php');
                    return false;
                } else {
                    $this->setApiUrl( EQARBASEURL_LIVE );
                }

                if ( !defined('EQARAUTHKEY_LIVE') ) {
                    throw new \Exception('Missing EQARAUTHKEY_LIVE in wp-config.php');
                    return false;
                } else {
                    $this->setApiKey( EQARAUTHKEY_LIVE );
                }

            }

        }

    }


    /**
     * Get the value of $apiUrl
     * @return string  The Url of the EQAR API
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }


    /**
     * Set the value of $apiUrl
     * @param   string  $url    The Url of the EQAR API
     * @return  na
     */
    public function setApiUrl( $url ) {
        $this->apiUrl = $url;
    }


    /**
     * Get the value of $apiKey
     * @return string  The bearer key of the EQAR API
     */
    public function getApiKey() {
        return $this->apiKey;
    }


    /**
     * Set the value of $apiUrl
     * @param  string  $key    The bearer key of the EQAR API
     * @return na
     */
    public function setApiKey( $key ) {
        $this->apiKey = $key;
    }



    /**
     * Construct the main rest client
     * @param   string      $path   Api query after the base path
     * @return  RestClient          The RestClient return object
     */
    public function eqar($path = false)
    {

        if (empty($path)) {
            throw new \Exception('No api query defined.');
        }

        $api = new RestClient([
            'base_url' => $this->getApiUrl() . $path,
            'format'   => EQARFORMAT,
            'headers'  => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->getApiKey(),
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
    public function getInstitution($institutionId = null, $history = false)
    {

        if (isset($institutionId) && !empty($institutionId)) {

            $reports    = $this->getReportInstitutionalByInstitution($institutionId);
            $programmes = $this->getReportProbrammesByInstitution($institutionId);

            $path = 'institutions/' . rawurlencode($institutionId) . '/?history=' . rawurlencode($history);
            $api = $this->eqar($path);
            $result = $api->get('');

            if ($result->info->http_code == 200) {

                $output = $result->decode_response();

                $output->reports    = $reports;
                $output->programmes = $programmes;

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
    public function getInstitutions($limit = 10, $offset = 0, $ordering = 'DESC', $query = false, $agency = false, $esg_activity = false, $country = false, $qf_ehea_level = false, $status = false, $report_year = false, $focus_country_is_crossborder = false, $history = false)
    {

        $path = 'institutions/?limit=' . rawurlencode($limit) . '&offset=' . rawurlencode($offset) . '&ordering=' . rawurlencode($ordering) . '&query=' . rawurlencode($query) . '&agency=' . rawurlencode($agency) . '&esg_activity=' . rawurlencode($esg_activity) . '&country=' . rawurlencode($country) . '&qf_ehea_level=' . rawurlencode($qf_ehea_level) . '&status=' . rawurlencode($status) . /*'&report_year=' . rawurlencode($report_year) . -- emergency */ '&focus_country_is_crossborder=' . rawurlencode($focus_country_is_crossborder);

        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
            return $result->decode_response();
        }

        return false;

    }


    /**
     * Get institution Reports
     * @param   int      $institutionId     Institution Id
     * @param   boolean  $history           Historical data parameter
     * @return  array    Reports
     */
    public function getReportInstitutionalByInstitution($institutionId = null)
    {

        $path = 'reports/institutional/by-institution/' . rawurlencode($institutionId) . '/?limit=200&offset=0';
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getReportProbrammesByInstitution($institutionId = null)
    {

        $path = 'reports/programme/by-institution/' . rawurlencode($institutionId) . '/?limit=200&offset=0';
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getAgency($agencyId = null, $history = false)
    {

        if (isset($agencyId) && !empty($agencyId)) {

            $path = 'agencies/' . rawurlencode($agencyId) . '/?history=' . rawurlencode($history);
            $api = $this->eqar($path);
            $result = $api->get('');
            $countries = $this->getAgencyCountries($agencyId, 'true'); //CT added quotes

            if ($result->info->http_code == 200) {
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

        $path = 'agencies/?limit=999&offset=0';
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getAgenciesByCountry($countryId = null, $history = false)
    {

        $path = 'agencies/based-in/' . rawurlencode($countryId) . '/?limit=999&offset=0&history=' . rawurlencode($history);
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getAgenciesByFocusCountry($countryId = null, $history = false)
    {

        $path = 'agencies/focusing-to/' . rawurlencode($countryId) . '/?limit=999&offset=0&history=' . rawurlencode($history);
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getCountry($countryId, $history = true)
    {

        $byCountry = $this->getAgenciesByCountry($countryId);
        $byCountryHistory = $this->getAgenciesByCountry($countryId, true);
        $byFocusCountry = $this->getAgenciesByFocusCountry($countryId);
        $byFocusCountryHistory = $this->getAgenciesByFocusCountry($countryId, true);

        $path = 'countries/' . rawurlencode($countryId) . '/?history=' . rawurlencode($history);
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {

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
        $eqar_governmental_member = false
    ) {

        $path = 'countries/?limit=' . rawurlencode($limit) . '&offset=' . rawurlencode($offset) . '&external_qaa=' . rawurlencode($external_qaa) . '&european_approach=' . rawurlencode($european_approach) . '&eqar_governmental_member=' . rawurlencode($eqar_governmental_member);
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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

        $path = 'countries/by-reports/?limit=999';
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
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
    public function getAgencyCountries($agencyId = null, $history = false)
    {

        $path = 'countries/by-agency-focus/' . rawurlencode($agencyId) . '/?limit=999&offset=0&history=' . $history;
        $api = $this->eqar($path);
        $result = $api->get('');

        if ($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Institutions by Counctry
     * @param   int      Country Id
     * @return  array    Array of Countries with Intstitutions
     */
    public function getInstitutionsByCountry($countryId = false)
    {

        $countries = $this->getCountries();
        $institutions = $this->getInstitutions();

        $output = [];

        foreach ($countries as $country) {

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
