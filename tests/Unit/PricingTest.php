<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Core\Models\PriceQuote;
use RunApi\Core\Models\PriceScheduleListResponse;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class PricingTest extends TestCase
{
    public function testReadsLiveScheduleAndCreatesTypedQuoteWithoutAuthentication(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['ETag' => '"v1"'], '{"as_of":"2026-07-23T12:00:00.000000Z","price_schedules":[{"service":"flux","action":"text_to_image","pricing_status":"available","catalog_status":"active","currency":"USD","billing_unit":"per_call","billing_strategy":"flat","cache_write_price_per_1m_cents":125,"billing_config":{}}]}'),
            new Response(200, [], '{"price_quote":{"service":"flux","action":"text_to_image","pricing_status":"available","currency":"USD","reservation_amount_cents":12,"estimate_basis":"exact","as_of":"2026-07-23T12:00:00.000000Z"}}'),
        ]);
        $client = new class (new ClientOptions(httpClient: $transport, maxRetries: 0)) extends BaseClient {};

        $schedule = $client->pricing->list(service: 'flux', ifNoneMatch: '"v1"');
        $quote = $client->pricing->quote('flux', 'text_to_image', 'flux-2-klein', ['prompt' => 'cube']);

        self::assertInstanceOf(PriceScheduleListResponse::class, $schedule);
        self::assertSame('flux', $schedule->priceSchedules[0]->service);
        self::assertSame(125, $schedule->priceSchedules[0]->cacheWritePricePer1mCents);
        self::assertSame('"v1"', $schedule->etag);
        self::assertInstanceOf(PriceQuote::class, $quote);
        self::assertSame('available', $quote->pricingStatus);
        self::assertSame(12, $quote->reservationAmountCents);
        self::assertSame('', $transport->requests[0]->getHeaderLine('Authorization'));
        self::assertSame('"v1"', $transport->requests[0]->getHeaderLine('If-None-Match'));
        self::assertSame('service=flux', $transport->requests[0]->getUri()->getQuery());
        self::assertSame('/api/v1/price_quotes', $transport->requests[1]->getUri()->getPath());
        self::assertSame(
            [
                'service' => 'flux',
                'action' => 'text_to_image',
                'model' => 'flux-2-klein',
                'params' => ['prompt' => 'cube'],
            ],
            json_decode((string) $transport->requests[1]->getBody(), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    public function testReturnsAValidNotModifiedScheduleDuringRevalidation(): void
    {
        $transport = new QueueHttpClient([
            new Response(304, ['ETag' => '"v1"']),
        ]);
        $client = new class (new ClientOptions(httpClient: $transport, maxRetries: 0)) extends BaseClient {};

        $schedule = $client->pricing->list(ifNoneMatch: '"v1"');

        self::assertTrue($schedule->notModified);
        self::assertSame('"v1"', $schedule->etag);
        self::assertSame([], $schedule->priceSchedules);
        self::assertSame('"v1"', $transport->requests[0]->getHeaderLine('If-None-Match'));
    }
}
