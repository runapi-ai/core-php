<?php

declare(strict_types=1);

namespace RunApi\Core;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Resources\Account;
use RunApi\Core\Resources\Files;
use RunApi\Core\Resources\Pricing;
use RunApi\Core\Resources\Uploads;

/**
 * Core RunAPI PHP client.
 *
 * Exposes typed model resources plus the universal Files, Uploads, and account resources.
 */
abstract class BaseClient
{
    /**
     * Persistent File lifecycle and temporary URL upload resource.
     */
    public readonly Files $files;
    /**
     * Universal account identity and balance resource.
     */
    public readonly Account $account;
    /** Live Price Schedule and Price Quote resource. */
    public readonly Pricing $pricing;
    /** Multipart Upload lifecycle resource. */
    public readonly Uploads $uploads;

    protected readonly HttpClient $http;

    /**
     * Initialize shared client resources with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        $this->http = new HttpClient($options);
        $this->files = new Files($this->http);
        $this->account = new Account($this->http);
        $this->pricing = new Pricing($this->http);
        $this->uploads = new Uploads($this->http);
    }
}
