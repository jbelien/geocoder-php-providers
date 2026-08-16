<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\UrbIS\Tests;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Provider\UrbIS\UrbIS;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @internal
 *
 * @coversNothing
 */
final class UrbISTest extends BaseTestCase
{
    public function testGeocodeWithLocalhostIPv4(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The UrbIS provider does not support IP addresses, only street addresses.');

        $provider = new UrbIS($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    public function testGeocodeWithLocalhostIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The UrbIS provider does not support IP addresses, only street addresses.');

        $provider = new UrbIS($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    public function testGeocodeWithRealIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The UrbIS provider does not support IP addresses, only street addresses.');

        $provider = new UrbIS($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testReverseQuery(): void
    {
        $provider = new UrbIS($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(50.841973, 4.362288)->withLocale('fr'));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertSame('1', $result->getStreetNumber());
        self::assertSame('Place des Palais', $result->getStreetName());
        self::assertSame('1000', $result->getPostalCode());
        self::assertSame('Bruxelles', $result->getLocality());
    }

    public function testGeocodeQuery(): void
    {
        $provider = new UrbIS($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('1 Place des Palais 1000 Bruxelles')->withLocale('fr'));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(50.841973, $result->getCoordinates()->getLatitude(), 0.00001);
        self::assertEqualsWithDelta(4.362288, $result->getCoordinates()->getLongitude(), 0.00001);
        self::assertSame('1', $result->getStreetNumber());
        self::assertSame('Place des Palais', $result->getStreetName());
        self::assertSame('1000', $result->getPostalCode());
        self::assertSame('Bruxelles', $result->getLocality());
    }

    protected function getCacheDir(): string
    {
        return __DIR__.'/.cached_responses';
    }
}
