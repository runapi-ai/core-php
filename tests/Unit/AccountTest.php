<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\Account;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class AccountTest extends TestCase
{
    public function testInfoSendsCorrectRequestAndHydratesResponse(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":1,"name":"Test User","email":"developer@runapi.ai","account":{"id":2,"name":"Acme","tier":"team"},"custom":"kept"}'),
        ]);
        $account = new Account(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $response = $account->info(new RequestOptions(headers: ['X-Test' => 'yes']));

        self::assertSame('GET', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/me', $transport->requests[0]->getUri()->getPath());
        self::assertSame('yes', $transport->requests[0]->getHeaderLine('X-Test'));
        self::assertSame(1, $response->id);
        self::assertSame('Test User', $response->name);
        self::assertSame('developer@runapi.ai', $response->email);
        self::assertSame(2, $response->account->id);
        self::assertSame('Acme', $response->account->name);
        self::assertSame('team', $response->account->toArray()['tier']);
        self::assertSame('kept', $response->toArray()['custom']);
    }

    public function testBalanceSendsCorrectRequestAndHydratesResponse(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"balance_cents":5000,"paid_balance_cents":4000,"bonus_balance_cents":1000,"spent_cents_today":100,"spent_cents_total":2000,"custom":true}'),
        ]);
        $account = new Account(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $response = $account->balance();

        self::assertSame('GET', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/me/balance', $transport->requests[0]->getUri()->getPath());
        self::assertSame(5000, $response->balanceCents);
        self::assertSame(4000, $response->paidBalanceCents);
        self::assertSame(1000, $response->bonusBalanceCents);
        self::assertSame(100, $response->spentCentsToday);
        self::assertSame(2000, $response->spentCentsTotal);
        self::assertTrue($response->toArray()['custom']);
    }
}
