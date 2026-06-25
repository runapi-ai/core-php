<?php

declare(strict_types=1);

namespace RunApi\Core;

/**
 * Shared defaults used by the RunAPI PHP SDK.
 */
final class Constants
{
    public const DEFAULT_BASE_URL = 'https://runapi.ai';
    public const BASE_URL_ENV_VAR = 'RUNAPI_BASE_URL';
    public const DEFAULT_TIMEOUT_SECONDS = 900;
    public const DEFAULT_POLL_INTERVAL_SECONDS = 2.0;
    public const DEFAULT_MAX_WAIT_SECONDS = 900.0;
    public const DEFAULT_MAX_RETRIES = 2;
    public const DEFAULT_RETRY_BASE_DELAY_SECONDS = 0.5;
    public const DEFAULT_RETRY_MAX_DELAY_SECONDS = 5.0;
    public const SDK_USER_AGENT = 'runapi-sdk-php/0.1.0';

    /** @var list<string> */
    public const IDEMPOTENT_METHODS = ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS'];

    /** @var list<int> */
    public const RETRYABLE_STATUS_CODES = [429, 500, 502, 503, 504];

    private function __construct()
    {
    }
}
