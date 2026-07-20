<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Server error returned by RunAPI.
 */
final class ServerException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 500,
        ?string $requestId = null,
        ?string $errorCode = 'server',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
