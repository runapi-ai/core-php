<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Authentication error returned by RunAPI.
 */
final class AuthenticationException extends RunApiException
{
    public function __construct(
        string $message,
        int $statusCode = 401,
        ?string $requestId = null,
        ?string $errorCode = 'authentication',
        mixed $details = null,
        ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $requestId, $errorCode, $details, $responseBody, $previous);
    }
}
