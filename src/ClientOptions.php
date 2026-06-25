<?php

declare(strict_types=1);

namespace RunApi\Core;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Client-level configuration shared by all RunAPI service operations.
 */
final readonly class ClientOptions
{
    /**
     * Create client configuration.
     */
    public function __construct(
        public ?string $apiKey = null,
        public ?string $baseUrl = null,
        public ?ClientInterface $httpClient = null,
        public ?RequestFactoryInterface $requestFactory = null,
        public ?StreamFactoryInterface $streamFactory = null,
        public int $timeoutSeconds = Constants::DEFAULT_TIMEOUT_SECONDS,
        public int $maxRetries = Constants::DEFAULT_MAX_RETRIES,
        public float $retryBaseDelaySeconds = Constants::DEFAULT_RETRY_BASE_DELAY_SECONDS,
        public float $retryMaxDelaySeconds = Constants::DEFAULT_RETRY_MAX_DELAY_SECONDS,
        /** @var array<string, string> */
        public array $headers = [],
        public string $userAgent = Constants::SDK_USER_AGENT,
    ) {
    }

    /**
     * Return the configured base URL after environment fallback and normalization.
     */
    public function resolvedBaseUrl(): string
    {
        $baseUrl = self::normalize($this->baseUrl)
            ?? self::normalize(getenv(Constants::BASE_URL_ENV_VAR) ?: null)
            ?? Constants::DEFAULT_BASE_URL;

        return rtrim($baseUrl, '/');
    }

    private static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
