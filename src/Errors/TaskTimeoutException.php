<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Raised when polling exceeds the configured maximum wait time.
 */
final class TaskTimeoutException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 0,
        ?string $requestId = null,
        ?string $errorCode = 'task_timeout',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
