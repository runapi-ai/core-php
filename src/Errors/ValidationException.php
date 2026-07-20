<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Client-side validation error before or while hydrating a response.
 */
final class ValidationException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 422,
        ?string $requestId = null,
        ?string $errorCode = 'validation',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
