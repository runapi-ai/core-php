<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Raised when polling reaches a failed task state.
 */
final class TaskFailedException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 0,
        ?string $requestId = null,
        ?string $errorCode = 'task_failed',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
