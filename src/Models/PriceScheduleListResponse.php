<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class PriceScheduleListResponse extends BaseModel
{
    /**
     * @param list<PriceSchedule> $priceSchedules
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $asOf,
        public array $priceSchedules,
        public bool $notModified,
        public ?string $etag,
        array $raw,
    ) {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        if (($raw['not_modified'] ?? false) === true) {
            return new self(
                asOf: null,
                priceSchedules: [],
                notModified: true,
                etag: Payload::optionalString($raw, 'etag'),
                raw: $raw,
            );
        }

        return new self(
            asOf: Payload::string($raw, 'as_of'),
            priceSchedules: Payload::listOf($raw, 'price_schedules', PriceSchedule::fromArray(...), required: true),
            notModified: false,
            etag: Payload::optionalString($raw, '_etag'),
            raw: $raw,
        );
    }
}
