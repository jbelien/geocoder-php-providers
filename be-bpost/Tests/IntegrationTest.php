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

use Geocoder\IntegrationTest\ProviderIntegrationTest;
use Geocoder\Provider\bpost\bpost;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class IntegrationTest extends ProviderIntegrationTest
{
    protected bool $testAddress = true;

    protected bool $testReverse = false;

    protected bool $testIpv4 = false;

    protected bool $testIpv6 = false;

    protected array $skippedTests = [
        'testGeocodeQuery' => 'Belgium only.',
    ];

    protected function createProvider(ClientInterface $httpClient)
    {
        return new bpost($httpClient, $_SERVER['BPOST_API_KEY']);
    }

    protected function getCacheDir(): string
    {
        return __DIR__.'/.cached_responses';
    }

    protected function getApiKey(): string
    {
        return $_SERVER['BPOST_API_KEY'];
    }
}
