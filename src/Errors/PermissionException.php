<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Permission error returned by RunAPI.
 */
final class PermissionException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 403,
        ?string $requestId = null,
        ?string $errorCode = 'permission',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
