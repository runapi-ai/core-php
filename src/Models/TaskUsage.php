<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/** RunAPI-owned cost on a completed Task envelope. */
final readonly class TaskUsage extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(public float $cost, array $raw)
    {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(Payload::float($raw, 'cost'), $raw);
    }
}
