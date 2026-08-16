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

use Geocoder\IntegrationTest\ProviderIntegrationTest;
use Geocoder\Provider\UrbIS\UrbIS;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class IntegrationTest extends ProviderIntegrationTest
{
    protected bool $testAddress = true;

    protected bool $testReverse = true;

    protected bool $testIpv4 = false;

    protected bool $testIpv6 = false;

    protected array $skippedTests = [
        'testGeocodeQuery' => 'UrbIS provider supports Brussels (Belgium) only.',
        'testReverseQuery' => 'UrbIS provider supports Brussels (Belgium) only.',
        'testGeocodeQueryWithNoResults' => 'UrbIS provider returns "wrong" results!',
        'testReverseQueryWithNoResults' => 'UrbIS provider returns "wrong" results!',
    ];

    protected function createProvider(ClientInterface $httpClient)
    {
        return new UrbIS($httpClient);
    }

    protected function getCacheDir(): string
    {
        return __DIR__.'/.cached_responses';
    }

    protected function getApiKey(): string
    {
        return '';
    }
}
