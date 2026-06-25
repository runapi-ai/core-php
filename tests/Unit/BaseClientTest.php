<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Core\Resources\Account;
use RunApi\Core\Resources\Files;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class BaseClientTest extends TestCase
{
    public function testExposesUniversalResources(): void
    {
        $client = new class (new ClientOptions(
            apiKey: 'test-key',
            httpClient: new QueueHttpClient([new Response(200, [], '{}')]),
        )) extends BaseClient {
        };

        self::assertInstanceOf(Files::class, $client->files);
        self::assertInstanceOf(Account::class, $client->account);
    }
}
