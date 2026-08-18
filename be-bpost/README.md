# 🇧🇪 Geocoder PHP "bpost" provider

> [Geocoder PHP](https://github.com/geocoder-php/Geocoder) is a PHP library which helps you build geo-aware applications by providing a powerful abstraction layer for geocoding manipulations.

This is the "bpost" provider for the [Geocoder PHP](https://github.com/geocoder-php/Geocoder).

![Latest Stable Version](https://poser.pugx.org/jbelien/geocoder-php-bpost-provider/v)
![Total Downloads](https://poser.pugx.org/jbelien/geocoder-php-bpost-provider/downloads)
![Monthly Downloads](https://poser.pugx.org/jbelien/geocoder-php-bpost-provider/d/monthly)
![License](https://poser.pugx.org/jbelien/geocoder-php-bpost-provider/license)

> [!NOTE]
> If you want to submit an Issue or create a Pull Request, please do so in the main repository: <https://github.com/jbelien/geocoder-php-providers>.

## Information

- **Coverage:** Belgium
- **Demo:** <https://www.bpost.be/site/en/webservice-address>

## Install

```bash
composer require jbelien/geocoder-php-bpost-provider
```

## Usage

See [Geocoder PHP README file](https://github.com/geocoder-php/Geocoder/blob/master/README.md).

```php
use Geocoder\Query\GeocodeQuery;

$httpClient = new \Http\Adapter\Guzzle6\Client();
$provider = new \Geocoder\Provider\bpost\bpost($httpClient);
$geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

// Query with unstructured address
$result = $geocoder->geocodeQuery(GeocodeQuery::create('5 Place des Palais 1000 Bruxelles'));

// Query with structured address
$query = GeocodeQuery::create('5 Place des Palais 1000 Bruxelles')
    ->withData('streetNumber', '5')
    ->withData('streetName', 'Place des Palais')
    ->withData('postalCode', '1000')
    ->withData('locality', 'Bruxelles');
$results = $geocoder->geocodeQuery($query);
```
