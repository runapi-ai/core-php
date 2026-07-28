<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class TaskRefund extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(public string $refundedAt, array $raw)
    {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(Payload::string($raw, 'refunded_at'), $raw);
    }
}
