<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\SPW\Tests;

use Geocoder\Exception\InvalidArgument;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\SPW\SPW;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @internal
 *
 * @coversNothing
 */
final class SPWTest extends BaseTestCase
{
    public function testGeocodeWithLocalhostIPv4(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The SPW provider does not support IP addresses, only street addresses.');

        $provider = new SPW($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    public function testGeocodeWithLocalhostIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The SPW provider does not support IP addresses, only street addresses.');

        $provider = new SPW($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    public function testGeocodeWithRealIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The SPW provider does not support IP addresses, only street addresses.');

        $provider = new SPW($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testHouseReverseQuery(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(50.461370, 4.840830));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertNotEmpty($results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf(Address::class, $result);
        self::assertSame('83', $result->getStreetNumber());
        self::assertSame('Chaussée de Charleroi', $result->getStreetName());
        self::assertSame('5000', $result->getPostalCode());
        self::assertSame('Namur', $result->getLocality());
        self::assertSame('Namur', $result->getSubLocality());
    }

    public function testHouseGeocodeQuery(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('Chaussée de Charleroi 83 5000 Namur'));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertNotEmpty($results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf(Address::class, $result);
        self::assertEqualsWithDelta(50.461370, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.840830, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('83', $result->getStreetNumber());
        self::assertSame('Chaussée de Charleroi', $result->getStreetName());
        self::assertSame('5000', $result->getPostalCode());
        self::assertSame('Namur', $result->getSubLocality());
        self::assertSame('Namur', $result->getLocality());
    }

    public function testStreetGeocodeQuery(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('Chaussée de Charleroi, Namur'));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertNotEmpty($results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf(Address::class, $result);
        self::assertEqualsWithDelta(50.449540, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.818282, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('Chaussée de Charleroi', $result->getStreetName());
        self::assertSame('Namur', $result->getLocality());
    }

    public function testCityGeocodeQuery(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('Namur'));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertNotEmpty($results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf(Address::class, $result);
        self::assertEqualsWithDelta(50.466390, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.866114, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('Namur', $result->getLocality());
    }

    public function testGeocodeLocaleException(): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('Locale must be one of "fr", "nl", or "de".');

        $provider = new SPW($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('Chaussée de Charleroi 83 5000 Namur')->withLocale('en'));
    }

    public function testReverseLocaleException(): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('Locale must be one of "fr", "nl", or "de".');

        $provider = new SPW($this->getMockedHttpClient());
        $provider->reverseQuery(ReverseQuery::fromCoordinates(50.461370, 4.840830)->withLocale('en'));
    }

    public function testGeocodeQueryWithNoResults(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('jsajhgsdkfjhsfkjhaldkadjaslgldasd'));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertEmpty($results);
    }

    public function testReverseQueryWithNoResults(): void
    {
        $provider = new SPW($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(0, 0));

        self::assertInstanceOf(AddressCollection::class, $results);
        self::assertEmpty($results);
    }

    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }
}
