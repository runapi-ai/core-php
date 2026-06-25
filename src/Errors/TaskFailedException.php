<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

/**
 * Raised when polling reaches a failed task state.
 */
final class TaskFailedException extends RunApiException
{
}
