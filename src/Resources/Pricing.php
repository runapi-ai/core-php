<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\PriceQuote;
use RunApi\Core\Models\PriceScheduleListResponse;
use RunApi\Core\Support\Payload;

/** Reads live Price Schedules and creates reservation quotes. */
final readonly class Pricing
{
    public function __construct(private HttpClient $http)
    {
    }

    public function list(?string $service = null, ?string $action = null, ?string $model = null, ?string $ifNoneMatch = null): PriceScheduleListResponse
    {
        $query = array_filter(['service' => $service, 'action' => $action, 'model' => $model], static fn (mixed $value): bool => $value !== null);
        $headers = $ifNoneMatch === null ? [] : ['If-None-Match' => $ifNoneMatch];

        return PriceScheduleListResponse::fromArray($this->http->request('get', '/api/v1/price_schedules', ['query' => $query, 'headers' => $headers, 'allow_not_modified' => true]));
    }

    /** @param array<string, mixed> $params
     */
    public function quote(string $service, string $action, ?string $model = null, array $params = []): PriceQuote
    {
        $response = $this->http->request('post', '/api/v1/price_quotes', [
            'body' => ['service' => $service, 'action' => $action, 'model' => $model, 'params' => $params],
        ]);

        return PriceQuote::fromArray(Payload::array($response, 'price_quote'));
    }
}
