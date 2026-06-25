<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * RunAPI account balance response.
 */
readonly class AccountBalanceResponse extends BaseModel
{
    /**
     * Create an account balance response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $balanceCents,
        public int $paidBalanceCents,
        public int $bonusBalanceCents,
        public int $spentCentsToday,
        public int $spentCentsTotal,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'balance_cents' => $balanceCents,
            'paid_balance_cents' => $paidBalanceCents,
            'bonus_balance_cents' => $bonusBalanceCents,
            'spent_cents_today' => $spentCentsToday,
            'spent_cents_total' => $spentCentsTotal,
        ] : $raw);
    }

    /**
     * Hydrate an account balance response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            balanceCents: Payload::int($raw, 'balance_cents'),
            paidBalanceCents: Payload::int($raw, 'paid_balance_cents'),
            bonusBalanceCents: Payload::int($raw, 'bonus_balance_cents'),
            spentCentsToday: Payload::int($raw, 'spent_cents_today'),
            spentCentsTotal: Payload::int($raw, 'spent_cents_total'),
            raw: $raw,
        );
    }
}
