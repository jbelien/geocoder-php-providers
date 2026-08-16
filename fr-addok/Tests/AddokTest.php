<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\Addok\Tests;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Provider\Addok\Addok;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @internal
 *
 * @coversNothing
 */
final class AddokTest extends BaseTestCase
{
    public function testGeocodeWithLocalhostIPv4(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The Addok provider does not support IP addresses, only street addresses.');

        $provider = Addok::withBANServer($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    public function testGeocodeWithLocalhostIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The Addok provider does not support IP addresses, only street addresses.');

        $provider = Addok::withBANServer($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    public function testGeocodeWithRealIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The Addok provider does not support IP addresses, only street addresses.');

        $provider = Addok::withBANServer($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testReverseQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(49.031526, 2.060164));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertSame('6', $result->getStreetNumber());
        self::assertSame('Quai de la Tourelle', $result->getStreetName());
        self::assertSame('95000', $result->getPostalCode());
        self::assertSame('Cergy', $result->getLocality());
        self::assertCount(3, $result->getAdminLevels());
        self::assertSame('Île-de-France', $result->getAdminLevels()->get(2)->getName());
        self::assertSame('Val-d\'Oise', $result->getAdminLevels()->get(3)->getName());
        self::assertSame('95', $result->getAdminLevels()->get(3)->getCode());
        self::assertSame('Cergy', $result->getAdminLevels()->get(4)->getName());
        self::assertSame('95127', $result->getAdminLevels()->get(4)->getCode());
    }

    public function testGeocodeQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('6 quai de la tourelle cergy'));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(49.031526, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(2.060164, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('6', $result->getStreetNumber());
        self::assertSame('Quai de la Tourelle', $result->getStreetName());
        self::assertSame('95000', $result->getPostalCode());
        self::assertSame('Cergy', $result->getLocality());
        self::assertCount(3, $result->getAdminLevels());
        self::assertSame('Île-de-France', $result->getAdminLevels()->get(2)->getName());
        self::assertSame('Val-d\'Oise', $result->getAdminLevels()->get(3)->getName());
        self::assertSame('95', $result->getAdminLevels()->get(3)->getCode());
        self::assertSame('Cergy', $result->getAdminLevels()->get(4)->getName());
        self::assertSame('95127', $result->getAdminLevels()->get(4)->getCode());
    }

    public function testGeocodeOnlyCityQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('Grenoble'));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(45.182828, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(5.724369, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertNull($result->getStreetNumber());
        self::assertNull($result->getStreetName());
        self::assertSame('38000', $result->getPostalCode());
        self::assertSame('Grenoble', $result->getLocality());
        self::assertCount(3, $result->getAdminLevels());
        self::assertSame('Auvergne-Rhône-Alpes', $result->getAdminLevels()->get(2)->getName());
        self::assertSame('Isère', $result->getAdminLevels()->get(3)->getName());
        self::assertSame('38', $result->getAdminLevels()->get(3)->getCode());
        self::assertSame('Grenoble', $result->getAdminLevels()->get(4)->getName());
        self::assertSame('38185', $result->getAdminLevels()->get(4)->getCode());
    }

    public function testGeocodeHouseNumberTypeQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->geocodeQuery(
            GeocodeQuery::create('20 avenue Kléber, Paris')->withData('type', Addok::TYPE_HOUSENUMBER)
        );

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertSame('20', $result->getStreetNumber());
        self::assertSame('Avenue Kléber', $result->getStreetName());
        self::assertSame('75016', $result->getPostalCode());
        self::assertSame('Paris', $result->getLocality());
        self::assertCount(4, $result->getAdminLevels());
        self::assertSame('Île-de-France', $result->getAdminLevels()->get(2)->getName());
        self::assertSame('Paris', $result->getAdminLevels()->get(3)->getName());
        self::assertSame('75', $result->getAdminLevels()->get(3)->getCode());
        self::assertSame('Paris', $result->getAdminLevels()->get(4)->getName());
        self::assertSame('75116', $result->getAdminLevels()->get(4)->getCode());
        self::assertSame('Paris 16e Arrondissement', $result->getAdminLevels()->get(5)->getName());
    }

    public function testGeocodeStreetTypeQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->geocodeQuery(
            GeocodeQuery::create('20 avenue Kléber, Paris')->withData('type', Addok::TYPE_STREET)
        );

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertNull($result->getStreetNumber());
        self::assertSame('Avenue Kléber', $result->getStreetName());
        self::assertSame('75016', $result->getPostalCode());
        self::assertSame('Paris', $result->getLocality());
        self::assertCount(4, $result->getAdminLevels());
        self::assertSame('Île-de-France', $result->getAdminLevels()->get(2)->getName());
        self::assertSame('Paris', $result->getAdminLevels()->get(3)->getName());
        self::assertSame('75', $result->getAdminLevels()->get(3)->getCode());
        self::assertSame('Paris', $result->getAdminLevels()->get(4)->getName());
        self::assertSame('75116', $result->getAdminLevels()->get(4)->getCode());
        self::assertSame('Paris 16e Arrondissement', $result->getAdminLevels()->get(5)->getName());
    }

    public function testGeocodeLocalityQuery(): void
    {
        $provider = Addok::withBANServer($this->getHttpClient());
        $results = $provider->geocodeQuery(
            GeocodeQuery::create('20 avenue Kléber, Paris')->withData('type', Addok::TYPE_LOCALITY)
        );

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertNull($result->getStreetNumber());
        self::assertNull($result->getStreetName());
        self::assertEqualsWithDelta(43.631962, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(1.380094, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('31700', $result->getPostalCode());
        self::assertSame('Blagnac', $result->getLocality());
    }

    protected function getCacheDir(): string
    {
        return __DIR__.'/.cached_responses';
    }
}
