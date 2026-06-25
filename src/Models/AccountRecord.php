<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * RunAPI account metadata.
 */
readonly class AccountRecord extends BaseModel
{
    /**
     * Create an account record value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $id,
        public string $name,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'name' => $name,
        ] : $raw);
    }

    /**
     * Hydrate an account record from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::int($raw, 'id'),
            name: Payload::string($raw, 'name'),
            raw: $raw,
        );
    }
}
