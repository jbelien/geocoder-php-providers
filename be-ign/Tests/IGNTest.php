<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\IGN\Tests;

use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Model\Address;
use Geocoder\Provider\IGN\IGN;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

/**
 * @internal
 *
 * @coversNothing
 */
final class IGNTest extends BaseTestCase
{
    public function testGeocodeWithLocalhostIPv4(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The IGN-NGI provider does not support IP addresses, only street addresses.');

        $provider = new IGN($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    public function testGeocodeWithLocalhostIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The IGN-NGI provider does not support IP addresses, only street addresses.');

        $provider = new IGN($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    public function testGeocodeWithRealIPv6(): void
    {
        $this->expectException(UnsupportedOperation::class);
        $this->expectExceptionMessage('The IGN-NGI provider does not support IP addresses, only street addresses.');

        $provider = new IGN($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testReverseQuery(): void
    {
        $provider = new IGN($this->getHttpClient());
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(50.991974, 5.351705)->withLimit(1));

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertSame('1', $result->getStreetNumber());
        self::assertSame('Trambergstraat', $result->getStreetName());
        self::assertSame('3520', $result->getPostalCode());
        self::assertSame('Zonhoven', $result->getLocality());
    }

    public function testGeocodeQuery(): void
    {
        $provider = new IGN($this->getHttpClient());
        $results = $provider->geocodeQuery(GeocodeQuery::create('Trambergstraat 1, 3520 Zonhoven')->withLimit(1));

        var_dump($results);

        self::assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        self::assertCount(1, $results);

        /** @var Address $result */
        $result = $results->first();
        self::assertInstanceOf('\Geocoder\Model\Address', $result);
        self::assertEqualsWithDelta(50.991974, $result->getCoordinates()->getLatitude(), 0.00001, '');
        self::assertEqualsWithDelta(5.351705, $result->getCoordinates()->getLongitude(), 0.00001, '');
        self::assertSame('1', $result->getStreetNumber());
        self::assertSame('Trambergstraat', $result->getStreetName());
        self::assertSame('3520', $result->getPostalCode());
        self::assertSame('Zonhoven', $result->getLocality());
    }

    protected function getCacheDir(): ?string
    {
        return null;
    }
}
