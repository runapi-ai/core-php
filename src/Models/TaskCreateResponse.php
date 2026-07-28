<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * Acknowledgement returned by create() before an async task starts processing.
 */
readonly class TaskCreateResponse extends BaseModel
{
    public ?TaskBillingFacts $billing;

    /**
     * Create a task creation response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $id,
        array $raw = [],
        public ?bool $taskReplayed = null,
        ?TaskBillingFacts $billing = null,
    ) {
        $this->billing = $billing ?? self::billing($raw);
        parent::__construct($raw === [] ? ['id' => $id, 'billing' => $this->billing?->toArray()] : $raw);
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
            billing: isset($raw['billing']) && is_array($raw['billing']) ? TaskBillingFacts::fromArray($raw['billing']) : null,
            raw: $raw,
            taskReplayed: Payload::optionalBool($raw, 'task_replayed'),
        );
    }

    /** @param array<string, mixed> $raw */
    private static function billing(array $raw): ?TaskBillingFacts
    {
        return isset($raw['billing']) && is_array($raw['billing']) ? TaskBillingFacts::fromArray($raw['billing']) : null;
    }
}
