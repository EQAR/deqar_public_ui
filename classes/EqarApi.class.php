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
     * EQAR API
     * @var RestClient  The instance to make requests
     */
    protected $api;

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

            $this->api = new RestClient([
                'base_url' => $this->getApiUrl(),
                'format'   => EQARFORMAT,
                'headers'  => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                ],
            ]);

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
     * Return the main rest client
     * @return  RestClient          The RestClient return object
     */
    public function eqar()
    {
        return $this->api;
    }


    /**
     * Get an Institution
     * @param   int      Institution Id
     * @param   boolean  Show historical data
     * @return  object   Institution object
     */
    public function getInstitution($institutionId = null, $history = 'true')
    {

        if (isset($institutionId) && !empty($institutionId)) {

            $reports    = $this->getReportInstitutionalByInstitution($institutionId, $history);
            $programmes = $this->getReportProgrammesByInstitution($institutionId, $history);

            $api = $this->eqar();
            $result = $api->get('institutions/' . rawurlencode($institutionId) . '/', array(
                'history' => $history,
            ));

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
    public function getInstitutions($limit = 10, $offset = 0, $ordering = 'DESC', $query = '', $agency = '', $esg_activity = '', $country = '', $qf_ehea_level = '', $status = '', $report_year = '', $focus_country_is_crossborder = '', $history = 'true')
    {
        $api = $this->eqar();
        $result = $api->get('institutions/', array(
            'limit' =>                          $limit,
            'offset' =>                         $offset,
            'query' =>                          $query,
            'agency' =>                         $agency,
            'esg_activity' =>                   $esg_activity,
            'country' =>                        $country,
            'qf_ehea_level' =>                  $qf_ehea_level,
            'status' =>                         $status,
            'report_year' =>                    $report_year,
            'focus_country_is_crossborder' =>   $focus_country_is_crossborder,
            'ordering' =>                       $ordering,
            'history' =>                        $history,
        ));

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
    public function getReportInstitutionalByInstitution($institutionId = null, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('reports/institutional/by-institution/' . rawurlencode($institutionId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ));

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
    public function getReportProgrammesByInstitution($institutionId = null, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('reports/programme/by-institution/' . rawurlencode($institutionId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ));

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
    public function getAgency($agencyId = null, $history = 'true')
    {

        if (isset($agencyId) && !empty($agencyId)) {

            $api = $this->eqar();
            $result = $api->get('agencies/' . rawurlencode($agencyId) . '/', array(
                'history' => $history,
            ));
            $countries = $this->getAgencyCountries($agencyId, $history);

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
     * @param   string   $registered      Select by current registration status (True, False or All)
     * @return  array    All Agencies
     */
    public function getAgencies($registered = 'True')
    {

        $api = $this->eqar();
        $result = $api->get('agencies/', array(
            'limit' => 999,
            'offset' => 0,
            'registered' => $registered,
        ));

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
    public function getAgenciesByCountry($countryId = null, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('agencies/based-in/' . rawurlencode($countryId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ));

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
    public function getAgenciesByFocusCountry($countryId = null, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('agencies/focusing-to/' . rawurlencode($countryId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ));

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
    public function getCountry($countryId, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('countries/' . rawurlencode($countryId) . '/', array(
            'history' => $history,
        ));

        $byFocusCountry = $this->getAgenciesByFocusCountry($countryId, $history);

        if ($result->info->http_code == 200) {

            $output = $result->decode_response();
            $output->agencies = [
                'byFocusCountry' => $byFocusCountry,
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
        $external_qaa = '',
        $european_approach = '',
        $eqar_governmental_member = ''
    ) {

        $api = $this->eqar();
        $result = $api->get('countries/', array(
            'limit' =>                      $limit,
            'offset' =>                     $offset,
            'external_qaa' =>               $external_qaa,
            'european_approach' =>          $european_approach,
            'eqar_governmental_member' =>   $eqar_governmental_member,
        ));

        if ($result->info->http_code == 200) {
            return $result->decode_response();
        }

        return false;

    }


    /**
     * Get a list of Countries
     * @param  boolean  Show historical data
     * @return array    All Countries
     */
    public function getCountriesByReports( $history = 'true' )
    {

        $api = $this->eqar();
        $result = $api->get('countries/by-reports/', array(
            'limit' => 999,
            'history' => $history,
        ));

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
    public function getAgencyCountries($agencyId = null, $history = 'true')
    {

        $api = $this->eqar();
        $result = $api->get('countries/by-agency-focus/' . rawurlencode($agencyId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ));

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
