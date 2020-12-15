# DEQAR public user interface

This is the code of the public user interface of the Database of External Quality Assurance Results (DEQAR) at <https://www.deqar.eu/>.

It uses the DEQAR Web API, a REST API provided by the [DEQAR backend](https://github.com/EQAR/eqar_backend). Refer to the [DEQAR documentation](https://docs.deqar.eu/web_api/#web-api) for details about the Web API and write to [deqar@eqar.eu](mailto:deqar@eqar.eu) to request access.

The public user interface is part of the [EQAR website](https://www.eqar.eu/), which is powered by [WordPress](https://wordpress.org/). The user interface consists of a number of specific page templates, which are part of the EQAR website theme. A number of general, not-DEQAR-related templates of the EQAR website have been removed from this repository.

The following constants should be set in `wp-config.php`:

```php
define('EQAR_ENV', 'TEST'); // one of LIVE or TEST

define('EQARBASEURL_LIVE', 'https://backend.deqar.eu/webapi/v2/browse/');
define('EQARAUTHKEY_LIVE', '*****'); // your API token

define('EQARBASEURL_TEST', 'https://backend.sandbox.deqar.eu/webapi/v2/browse/');
define('EQARAUTHKEY_TEST', '*****'); // your API token

// most data fetched from Web API will be stored as transient, set here for how many seconds
define('EQAR_CACHE_TIME', 3600);

// countries to show on map in addition to EHEA members: Monaco, Kosovo
define('EQAR_MAP_ADDITIONAL_COUNTRIES', array(115, 268) );

// countries to show on map as circles: Andorra, Liechtenstein, Luxembourg, Malta, Monaco, San Marino, Holy See
define('EQAR_MAP_MICRO_STATES', array(4, 99, 101, 108, 115, 148, 188) );
```

