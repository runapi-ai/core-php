<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class PriceSchedule extends BaseModel
{
    /**
     * @param array<string, mixed> $billingConfig
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $service,
        public string $action,
        public ?string $model,
        public string $pricingStatus,
        public string $catalogStatus,
        public string $currency,
        public string $billingUnit,
        public string $billingStrategy,
        public ?int $unitPriceCents,
        public ?int $inputPricePer1mCents,
        public ?int $outputPricePer1mCents,
        public ?int $cacheReadPricePer1mCents,
        public ?int $cacheWrite5mPricePer1mCents,
        public ?int $cacheWrite1hPricePer1mCents,
        public array $billingConfig,
        array $raw,
    ) {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        $int = static fn (string $key): ?int => isset($raw[$key]) && is_int($raw[$key]) ? $raw[$key] : null;

        return new self(
            service: Payload::string($raw, 'service'),
            action: Payload::string($raw, 'action'),
            model: Payload::optionalString($raw, 'model'),
            pricingStatus: Payload::string($raw, 'pricing_status'),
            catalogStatus: Payload::string($raw, 'catalog_status'),
            currency: Payload::string($raw, 'currency'),
            billingUnit: Payload::string($raw, 'billing_unit'),
            billingStrategy: Payload::string($raw, 'billing_strategy'),
            unitPriceCents: $int('unit_price_cents'),
            inputPricePer1mCents: $int('input_price_per_1m_cents'),
            outputPricePer1mCents: $int('output_price_per_1m_cents'),
            cacheReadPricePer1mCents: $int('cache_read_price_per_1m_cents'),
            cacheWrite5mPricePer1mCents: $int('cache_write_5m_price_per_1m_cents'),
            cacheWrite1hPricePer1mCents: $int('cache_write_1h_price_per_1m_cents'),
            billingConfig: isset($raw['billing_config']) && is_array($raw['billing_config']) ? $raw['billing_config'] : [],
            raw: $raw,
        );
    }
}
