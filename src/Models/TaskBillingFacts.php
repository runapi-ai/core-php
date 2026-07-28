<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

/** Persisted reservation, settlement, and refund facts for a task. */
final readonly class TaskBillingFacts extends BaseModel
{
    public function __construct(
        public ?TaskReservation $reservation,
        public ?TaskSettlement $settlement,
        public ?TaskRefund $refund,
        array $raw,
    ) {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            reservation: isset($raw['reservation']) && is_array($raw['reservation']) ? TaskReservation::fromArray($raw['reservation']) : null,
            settlement: isset($raw['settlement']) && is_array($raw['settlement']) ? TaskSettlement::fromArray($raw['settlement']) : null,
            refund: isset($raw['refund']) && is_array($raw['refund']) ? TaskRefund::fromArray($raw['refund']) : null,
            raw: $raw,
        );
    }
}
