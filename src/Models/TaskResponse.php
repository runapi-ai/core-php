<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * Base async task response with task id, lifecycle status, and optional error message.
 */
readonly class TaskResponse extends BaseModel
{
    /**
     * Create a task response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $id,
        public string $status,
        public ?string $error = null,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'status' => $status,
            'error' => $error,
        ] : $raw);
    }

    /**
     * Read the optional task error message from a response payload.
     *
     * @param array<string, mixed> $raw
     */
    protected static function error(array $raw): ?string
    {
        return Payload::optionalString($raw, 'error');
    }
}
