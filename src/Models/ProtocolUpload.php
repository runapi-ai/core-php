<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class ProtocolUpload extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        $file = isset($raw['file']) && is_array($raw['file']) ? ProtocolFile::fromArray($raw['file']) : null;
        return new self(
            Payload::string($raw, 'id'),
            Payload::string($raw, 'object'),
            Payload::int($raw, 'bytes'),
            Payload::int($raw, 'created_at'),
            Payload::string($raw, 'filename'),
            Payload::string($raw, 'purpose'),
            Payload::string($raw, 'status'),
            Payload::int($raw, 'expires_at'),
            $file,
            $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $id,
        public string $object,
        public int $bytes,
        public int $createdAt,
        public string $filename,
        public string $purpose,
        public string $status,
        public int $expiresAt,
        public ?ProtocolFile $file,
        array $raw,
    ) {
        parent::__construct($raw);
    }
}
