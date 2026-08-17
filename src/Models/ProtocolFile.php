<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class ProtocolFile extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            object: Payload::string($raw, 'object'),
            bytes: Payload::int($raw, 'bytes'),
            createdAt: Payload::int($raw, 'created_at'),
            expiresAt: isset($raw['expires_at']) && is_int($raw['expires_at']) ? $raw['expires_at'] : null,
            filename: Payload::string($raw, 'filename'),
            purpose: Payload::string($raw, 'purpose'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $id,
        public string $object,
        public int $bytes,
        public int $createdAt,
        public ?int $expiresAt,
        public string $filename,
        public string $purpose,
        array $raw,
    ) {
        parent::__construct($raw);
    }
}
