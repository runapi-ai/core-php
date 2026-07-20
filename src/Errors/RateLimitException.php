<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Rate limit error returned by RunAPI.
 */
final class RateLimitException extends RunApiException
{
    /**
     * Create a rate limit exception with optional retry timing.
     */
    public function __construct(
        string $message,
        public readonly ?float $retryAfterSeconds = null,
        int $statusCode = 429,
        ?string $requestId = null,
        ?string $errorCode = 'rate_limit',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            $statusCode,
            $requestId,
            $errorCode,
            $details,
            $responseBody,
            $previous,
        );
    }
}
