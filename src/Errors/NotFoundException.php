<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Requested RunAPI resource was not found.
 */
final class NotFoundException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 404,
        ?string $requestId = null,
        ?string $errorCode = 'not_found',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
