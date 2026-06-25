<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

use RuntimeException;
use Throwable;

/**
 * Base exception for all RunAPI PHP SDK errors.
 */
class RunApiException extends RuntimeException
{
    /**
     * Create a RunAPI exception with HTTP and response context.
     */
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly ?string $requestId = null,
        public readonly ?string $errorCode = null,
        public readonly mixed $details = null,
        public readonly ?string $responseBody = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
