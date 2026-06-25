<?php

declare(strict_types=1);

namespace RunApi\Core;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Resources\Account;
use RunApi\Core\Resources\Files;

/**
 * Core RunAPI PHP client.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
abstract class BaseClient
{
    /**
     * Universal file upload resource.
     */
    public readonly Files $files;
    /**
     * Universal account identity and balance resource.
     */
    public readonly Account $account;

    protected readonly HttpClient $http;

    /**
     * Initialize shared client resources with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        $this->http = new HttpClient($options);
        $this->files = new Files($this->http);
        $this->account = new Account($this->http);
    }
}
