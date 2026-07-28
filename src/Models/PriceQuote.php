<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class PriceQuote extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $service,
        public string $action,
        public ?string $model,
        public string $pricingStatus,
        public string $currency,
        public int $reservationAmountCents,
        public string $estimateBasis,
        public ?string $asOf,
        array $raw,
    ) {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            service: Payload::string($raw, 'service'),
            action: Payload::string($raw, 'action'),
            model: Payload::optionalString($raw, 'model'),
            pricingStatus: Payload::string($raw, 'pricing_status'),
            currency: Payload::string($raw, 'currency'),
            reservationAmountCents: Payload::int($raw, 'reservation_amount_cents'),
            estimateBasis: Payload::string($raw, 'estimate_basis'),
            asOf: Payload::optionalString($raw, 'as_of'),
            raw: $raw,
        );
    }
}
