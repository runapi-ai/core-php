<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * Acknowledgement returned by create() before an async task starts processing.
 */
readonly class TaskCreateResponse extends BaseModel
{
    /**
     * Create a task creation response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $id,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? ['id' => $id] : $raw);
    }

    /**
     * Hydrate a task creation response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            raw: $raw,
        );
    }
}
