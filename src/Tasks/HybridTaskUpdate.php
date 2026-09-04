<?php

declare(strict_types=1);

namespace RunApi\Core\Tasks;

use RunApi\Core\Http\RawResponse;

/** One observable state emitted while following an accepted Task Result. */
final readonly class HybridTaskUpdate
{
    public function __construct(
        public HybridTask $task,
        public string $status,
        public RawResponse $response,
        public ?string $error = null,
    ) {
    }

    public function terminal(): bool
    {
        return $this->status === 'completed' || $this->status === 'failed';
    }
}
