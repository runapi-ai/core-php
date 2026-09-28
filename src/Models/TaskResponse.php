<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * Base async task response with task id, lifecycle status, and optional error message.
 */
readonly class TaskResponse extends BaseModel
{
    public ?TaskUsage $usage;

    /**
     * Create a task response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $id,
        public string $status,
        public ?string $error = null,
        array $raw = [],
        ?TaskUsage $usage = null,
    ) {
        $this->usage = $usage ?? self::usage($raw);
        parent::__construct($raw === [] ? [
            'id' => $id,
            'status' => $status,
            'error' => $error,
            'usage' => $this->usage?->toArray()] : $raw);
    }

    /**
     * Read the optional task error message from a response payload.
     *
     * @param array<string, mixed> $raw
     */
    protected static function error(array $raw): ?string
    {
        return Payload::optionalString($raw, 'error');
    }

    /** @param array<string, mixed> $raw */
    private static function usage(array $raw): ?TaskUsage
    {
        return isset($raw['usage']) && is_array($raw['usage']) ? TaskUsage::fromArray($raw['usage']) : null;
    }
}
