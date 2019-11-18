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

            if ( !defined('EQAR_CACHE_TIME') ) {
                define('EQAR_CACHE_TIME', 60); // unless higher time set, we cache for 1 minute
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
     * Run a GET request using the RestClient instance
     * @return  Array               The data received
     */
    public function get($url, $parameters = array())
    {
        $result = $this->api->get($url, $parameters);

        if ($result->info->http_code == 200) {

            return $result->decode_response();

        } else {

            Site::do404($result->info->http_code, $result->info->url."\n".join($result->response_status_lines,"\n")."\n\n".$result->response);

        }
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

            return Timber\Helper::transient('DEQAR:institution-'.$institutionId.'-history='.$history, function() use ($institutionId, $history) {

                $output = $this->get('institutions/' . rawurlencode($institutionId) . '/', array(
                    'history' => $history,
                ));

                $output->reports    = $this->getReportInstitutionalByInstitution($institutionId, $history);
                $output->programmes = $this->getReportProgrammesByInstitution($institutionId, $history);

                return $output;

            }, EQAR_CACHE_TIME);

        } else {
            Site::do404(400, "DEQAR institution ID must be provided.");
        }

    }


    /**
     * Get an Institution using the ETER ID
     * @param   string   ETER ID
     * @param   boolean  Show historical data
     * @return  object   Institution object
     */
    public function getInstitutionByEterId($eterId, $history = 'true')
    {

        if (!empty($eterId)) {

            $output = $this->get('institutions/by-eter/' . rawurlencode($eterId) . '/', array(
                'history' => $history,
            ));

            $output->reports    = $this->getReportInstitutionalByInstitution($output->id, $history);
            $output->programmes = $this->getReportProgrammesByInstitution($output->id, $history);

            return $output;

        } else {
            Site::do404(400, "ETER ID must be provided.");
        }

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
    public function getInstitutions($parameters)
    {
        return $this->get('institutions/', $parameters);
    }


    /**
     * Get single Report
     * @param   int     $reportId           Report ID
     * @return  array                       Report record
     */
    public function getReport ($reportId)
    {

        if (isset($reportId) && !empty($reportId)) {

            return $this->get('reports/' . rawurlencode($reportId) . '/');

        } else {
            Site::do404(400, "DEQAR report ID must be provided.");
        }

    }

    /**
     * Search reports
     * @param   array   $parameters         Array of search parameters
     * @return  array                       Array of matching report records
     */
    public function getReports ($parameters)
    {
        return $this->get('reports/', $parameters);
    }

    /**
     * Get institution Reports
     * @param   int      $institutionId     Institution Id
     * @param   boolean  $history           Historical data parameter
     * @return  array    Reports
     */
    public function getReportInstitutionalByInstitution($institutionId = null, $history = 'true')
    {

        return $this->get('reports/institutional/by-institution/' . rawurlencode($institutionId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ))->results;

    }


    /**
     * Get institution Programmes
     * @param   int      $institutionId     Institution Id
     * @param   boolean  $history           Historical data parameter
     * @return  array    Programmes
     */
    public function getReportProgrammesByInstitution($institutionId = null, $history = 'true')
    {

        return $this->get('reports/programme/by-institution/' . rawurlencode($institutionId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ))->results;

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

            $output = $this->get('agencies/' . rawurlencode($agencyId) . '/', array(
                'history' => $history,
            ));
            $output->countries = $this->getAgencyCountries($agencyId, $history);

            return $output;

        } else {
            Site::do404(400, "DEQAR agency ID has to be provided.");
        }


    }


    /**
     * Get all Agencies
     * @param   string   $registered      Select by current registration status (True, False or All)
     * @return  array    All Agencies
     */
    public function getAgencies($registered = 'True')
    {

        return Timber\Helper::transient('DEQAR:agencies-registered='.$registered, function() use ($registered) {

            return $this->get('agencies/', array(
                'limit' => 999,
                'offset' => 0,
                'registered' => $registered,
            ))->results;

        }, EQAR_CACHE_TIME);

    }


    /**
     * Get a list of Agencies indexed by ID
     * @return array    All Agencies, ID as key
     */
    public function getAgenciesByID ()
    {
        $result = array();

        foreach($this->getAgencies() as $agency) {
            $result[$agency->id] = $agency;
        }

        return($result);
    }


    /**
     * Get a list of Agencies indexed by Name (= acronym)
     * @return array    All Agencies, acronym as key
     */
    public function getAgenciesByName ()
    {
        $result = array();

        foreach($this->getAgencies() as $agency) {
            $result[$agency->acronym_primary] = $agency;
        }

        return($result);
    }


    /**
     * Get all Agencies for a Country
     * @param   int      $countryId     Country Id
     * @param   boolean  $history       Show historical data
     * @return  array                   All Agencies of a certain country
     */
    public function getAgenciesByCountry($countryId = null, $history = 'true')
    {

        return $this->get('agencies/based-in/' . rawurlencode($countryId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ))->results;

    }


    /**
     * Get all Agencies for a Focus Country
     * @param   int      $countryId     Country Id
     * @param   boolean  $history       Show historical data
     * @return  array                   All Agencies of a certain country
     */
    public function getAgenciesByFocusCountry($countryId = null, $history = 'true')
    {

        return $this->get('agencies/focusing-to/' . rawurlencode($countryId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ))->results;

    }


    /**
     * Get a Country by ID
     * @param   int      Country Id
     * @param   boolean  Show historical data
     * @return  object   Country object
     */
    public function getCountry($countryId, $history = 'true')
    {

        $output = $this->get('countries/' . rawurlencode($countryId) . '/', array(
            'history' => $history,
        ));

        $output->agencies = [
            'byFocusCountry' => $this->getAgenciesByFocusCountry($countryId, $history)
        ];

        return $output;

    }


    /**
     * Get a list of EHEA countries
     * @param  boolean $external_qaa                Query External QAA
     * @param  boolean $european_approach           Query External European Approach
     * @param  boolean $eqar_governmental_member    Query Eqar governmental members
     * @return array                                All Countries
     */
    public function getCountries(
        $external_qaa = '',
        $european_approach = '',
        $eqar_governmental_member = ''
    ) {

        return Timber\Helper::transient('DEQAR:countries-CBQA='.$external_qaa.'-EQAJP='.$european_approach.'-MEMBER='.$eqar_governmental_member,
            function() use ($external_qaa, $european_approach, $eqar_governmental_member) {
                return $this->get('countries/', array(
                    'external_qaa' =>               $external_qaa,
                    'european_approach' =>          $european_approach,
                    'eqar_governmental_member' =>   $eqar_governmental_member,
                ));

            }, EQAR_CACHE_TIME);

    }


    /**
     * Get a list of Countries
     * @param  boolean  Show historical data
     * @return array    All Countries
     */
    public function getCountriesByReports()
    {

        return Timber\Helper::transient('DEQAR:countriesByReports', function() {

            return $this->get('countries/by-reports/');

        }, EQAR_CACHE_TIME);

    }


    /**
     * Get a list of Countries by ID
     * @return array    All Countries
     */
    public function getCountriesByReportsByID ()
    {
        $result = array();

        foreach($this->getCountriesByReports() as $country) {
            $result[$country->id] = $country;
        }

        return($result);
    }


    /**
     * Get a list of Countries by Name
     * @return array    All Countries
     */
    public function getCountriesByReportsByName ()
    {
        $result = array();

        foreach($this->getCountriesByReports() as $country) {
            $result[$country->name_english] = $country;
        }

        return($result);
    }


    /**
     * Get all Countries where Agency operates
     * @param   int      Agency Id
     * @param   boolean  Show historical data
     * @return  array    All Countries
     */
    public function getAgencyCountries($agencyId = null, $history = 'true')
    {

        return $this->get('countries/by-agency-focus/' . rawurlencode($agencyId) . '/', array(
            'limit' => 999,
            'offset' => 0,
            'history' => $history,
        ))->results;

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


    /**
     * Get all EQAR decisions on agencies
     * @return  array    All decisions
     */
    public function getDecisions()
    {

        return Timber\Helper::transient('DEQAR:agencyDecisions', function() {

            return $this->get('agencies/decisions/');

        }, EQAR_CACHE_TIME);

    }

}
