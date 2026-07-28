<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class TaskSettlement extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(public int $chargedAmountCents, public int $amountMicroCents, array $raw)
    {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(Payload::int($raw, 'charged_amount_cents'), Payload::int($raw, 'amount_micro_cents'), $raw);
    }
}
