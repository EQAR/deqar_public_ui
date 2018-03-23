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
     * cUrl Bearer auth token
     * @var string
     */
    const EQARAUTHKEY = '03a64a808705672b4fc61f0da1fe28d430250cd4';


    /**
     * Construct the main rest client
     * @param   string      $path   Api query after the base path
     * @return  RestClient          The RestClient return object
     */
    public function eqar( $path = false ) {

        if ( empty($path) ) {
            return false;
        }

        $api = new RestClient([
            'base_url'  => self::EQARBASEURL . $path,
            'format'    => self::EQARFORMAT,
            'headers'   => [ 'Authorization' => 'Bearer ' . self::EQARAUTHKEY ],
        ]);

        return $api;

    }


    /**
     * Get an Institution
     * @return  array    Institution
     * @param   int      Institution Id
     */
    public function getInstitution( $institutionId = null )
    {

        if ( isset($institutionId) && !empty($institutionId) ) {

            $path       = 'institutions/' . $institutionId . '/?history=false';
            $api        = $this->eqar( $path );
            $result     = $api->get('');

            if($result->info->http_code == 200) {
                $output = $result->decode_response();
                return $output;
            }

        }

        return false;

    }


    /**
     * Get all Institutions
     * @return  array    All Institutions
     */
    public function getInstitutions()
    {

        $path   = 'institutions/?limit=999&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get an Agency
     * @return  array    All Agencies
     * @param   int      Agency Id
     */
    public function getAgency( $agencyId = null )
    {

        if ( isset($agencyId) && !empty($agencyId) ) {

            $path       = 'agencies/' . $agencyId . '/?history=false';
            $api        = $this->eqar( $path );
            $result     = $api->get('');
            $countries  = $this->getAgencyCountries($agencyId);

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
     * Get all Countries
     * @return  array    All Countries
     */
    public function getCountries()
    {

        $path   = 'countries/?limit=999&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Countries
     * @return  array    All Countries
     */
    public function getAgencyCountries( $agencyId = false )
    {

        $path   = 'countries/by-agency-focus/' . $agencyId . '/?limit=999&offset=0';
        $api    = $this->eqar( $path );
        $result = $api->get('');

        if($result->info->http_code == 200) {
            return $result->decode_response()->results;
        }

        return false;

    }


    /**
     * Get all Institutions by Counctry
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
