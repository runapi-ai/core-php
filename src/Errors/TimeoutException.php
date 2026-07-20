<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Request timeout while calling RunAPI.
 */
final class TimeoutException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 0,
        ?string $requestId = null,
        ?string $errorCode = 'timeout',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
