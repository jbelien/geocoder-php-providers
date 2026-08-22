<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\IGN;

use Geocoder\Collection;
use Geocoder\Exception\InvalidArgument;
use Geocoder\Exception\InvalidServerResponse;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Model\AddressBuilder;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Psr\Http\Client\ClientInterface;

final class IGN extends AbstractHttpProvider implements Provider
{
    /**
     * @var string
     */
    public const GEOCODE_ENDPOINT_URL = 'https://geosearch.ngi.be/search/text?q=%s&projection=4326&source=ALL_SOURCES&responseFormat=GeoJSON&searchMode=FULL';

    /**
     * @var string
     */
    public const REVERSE_ENDPOINT_URL = 'https://geo.api.vlaanderen.be/geolocation/v4/Location?latlon=%F,%F&c=%d';

    /**
     * @param ClientInterface $client an HTTP adapter
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);
    }

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $address = $query->getText();
        // This API does not support IP
        if (filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('The IGN-NGI provider does not support IP addresses, only street addresses.');
        }

        // Save a request if no valid address entered
        if (empty($address)) {
            throw new InvalidArgument('Address cannot be empty.');
        }

        $url = \sprintf(
            self::GEOCODE_ENDPOINT_URL,
            urlencode($address)
        );
        $json = $this->executeQuery($url);

        // no result
        if (0 === $json->responseHeader->numFound || !isset($json->features)) {
            return new AddressCollection([]);
        }

        $builders = [];
        $street = null;

        foreach ($json->features as $feature) {
            if ($feature->properties->_type === 'ADDRESS') {
                $builder = new AddressBuilder($this->getName());

                $streetNumber = $feature->properties->houseNumber;
                if (isset($feature->properties->houseBox) && !empty($feature->properties->houseBox)) {
                    $streetNumber .= '/'.$feature->properties->houseBox;
                }

                $builder->setCoordinates($feature->geometry->coordinates[1], $feature->geometry->coordinates[0])
                    ->setStreetNumber($streetNumber)
                ;

                $builders[] = $builder;
            } else if ($feature->properties->_type === 'STREET') {
                $street = $feature->properties->details->names[0]->spelling;
            } else if ($feature->properties->_type === 'STREET') {
                $street = $feature->properties->details->names[0]->spelling;
            }
        }

        foreach ($builders as $address) {
            if (null !== $street) {
                $address->setStreetName($street);
            }
        }

        return new AddressCollection(array_map(static function (AddressBuilder $builder) {
            return $builder->build();
        }, $builders));
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        $coordinates = $query->getCoordinates();

        $url = \sprintf(self::REVERSE_ENDPOINT_URL, $coordinates->getLatitude(), $coordinates->getLongitude(), $query->getLimit());
        $json = $this->executeQuery($url);

        // no result
        if (empty($json->LocationResult)) {
            return new AddressCollection([]);
        }

        $results = [];
        foreach ($json->LocationResult as $location) {
            $streetName = !empty($location->Thoroughfarename) ? $location->Thoroughfarename : null;
            $housenumber = !empty($location->Housenumber) ? $location->Housenumber : null;
            $municipality = !empty($location->Municipality) ? $location->Municipality : null;
            $zipcode = !empty($location->Zipcode) ? $location->Zipcode : null;

            $builder = new AddressBuilder($this->getName());
            $builder->setCoordinates($location->Location->Lat_WGS84, $location->Location->Lon_WGS84)
                ->setStreetNumber($housenumber)
                ->setStreetName($streetName)
                ->setLocality($municipality)
                ->setPostalCode($zipcode)
                ->setBounds(
                    $location->BoundingBox->LowerLeft->Lat_WGS84,
                    $location->BoundingBox->LowerLeft->Lon_WGS84,
                    $location->BoundingBox->UpperRight->Lat_WGS84,
                    $location->BoundingBox->UpperRight->Lon_WGS84
                )
            ;

            $results[] = $builder->build();
        }

        return new AddressCollection($results);
    }

    public function getName(): string
    {
        return 'ign';
    }

    private function executeQuery(string $url): \stdClass
    {
        $content = $this->getUrlContents($url);
        $json = json_decode($content);
        // API error
        if (!isset($json)) {
            throw InvalidServerResponse::create($url);
        }

        return $json;
    }
}
