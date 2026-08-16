<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\bpost\Tests;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Provider\bpost\bpost;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @internal
 *
 * @coversNothing
 */
final class bpostTest extends BaseTestCase
{
    public function testGeocodeWithLocalhostIPv4(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The bpost provider does not support IP addresses, only street addresses.');

        $provider = new bpost($this->getMockedHttpClient(), $_SERVER['BPOST_API_KEY']);
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    public function testGeocodeWithLocalhostIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The bpost provider does not support IP addresses, only street addresses.');

        $provider = new bpost($this->getMockedHttpClient(), $_SERVER['BPOST_API_KEY']);
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    public function testGeocodeWithRealIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The bpost provider does not support IP addresses, only street addresses.');

        $provider = new bpost($this->getMockedHttpClient(), $_SERVER['BPOST_API_KEY']);
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testReverseQuery(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The bpost provider does not support reverse geocoding.');

        $provider = new bpost($this->getMockedHttpClient(), $_SERVER['BPOST_API_KEY']);
        $provider->reverseQuery(ReverseQuery::fromCoordinates(0, 0));
    }

    public function testGeocodeQuery(): void
    {
        $provider = new bpost($this->getHttpClient($_SERVER['BPOST_API_KEY']), $_SERVER['BPOST_API_KEY']);
        $results = $provider->geocodeQuery(GeocodeQuery::create('5 Place des Palais 1000 Bruxelles'));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(50.842931, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.361186, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('5', $result->getStreetNumber());
        self::assertSame('PLACE DES PALAIS', $result->getStreetName());
        self::assertSame('1000', $result->getPostalCode());
        self::assertSame('BRUXELLES', $result->getLocality());
        self::assertSame('BELGIQUE', $result->getCountry());
    }

    public function testGeocodeQueryWithData(): void
    {
        $query = GeocodeQuery::create('5 Place des Palais 1000 Bruxelles')
            ->withData('streetNumber', '5')
            ->withData('streetName', 'Place des Palais')
            ->withData('postalCode', '1000')
            ->withData('locality', 'Bruxelles')
        ;

        $provider = new bpost($this->getHttpClient($_SERVER['BPOST_API_KEY']), $_SERVER['BPOST_API_KEY']);
        $results = $provider->geocodeQuery($query);

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(50.842931, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.361186, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('5', $result->getStreetNumber());
        self::assertSame('PLACE DES PALAIS', $result->getStreetName());
        self::assertSame('1000', $result->getPostalCode());
        self::assertSame('BRUXELLES', $result->getLocality());
        self::assertSame('BELGIQUE', $result->getCountry());
    }

    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }
}
