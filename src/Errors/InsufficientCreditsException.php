<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Insufficient account credits error returned by RunAPI.
 */
final class InsufficientCreditsException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 402,
        ?string $requestId = null,
        ?string $errorCode = 'insufficient_credits',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
