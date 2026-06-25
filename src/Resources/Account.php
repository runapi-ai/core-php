<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\AccountBalanceResponse;
use RunApi\Core\Models\AccountInfoResponse;
use RunApi\Core\RequestOptions;

/**
 * Universal resource for account identity and balance lookups.
 */
final readonly class Account
{
    private const INFO_ENDPOINT = '/api/v1/me';
    private const BALANCE_ENDPOINT = '/api/v1/me/balance';

    /**
     * Create a resource using the shared RunAPI HTTP transport.
     */
    public function __construct(private HttpClient $http)
    {
    }

    /**
     * Fetch account profile information.
     */
    public function info(?RequestOptions $options = null): AccountInfoResponse
    {
        return AccountInfoResponse::fromArray($this->http->request('GET', self::INFO_ENDPOINT, [
            'options' => $options,
        ]));
    }

    /**
     * Fetch current account balance and usage totals.
     */
    public function balance(?RequestOptions $options = null): AccountBalanceResponse
    {
        return AccountBalanceResponse::fromArray($this->http->request('GET', self::BALANCE_ENDPOINT, [
            'options' => $options,
        ]));
    }
}
