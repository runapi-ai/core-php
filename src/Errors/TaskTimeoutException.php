<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Raised when polling exceeds the configured maximum wait time.
 */
final class TaskTimeoutException extends RunApiException
{
}
