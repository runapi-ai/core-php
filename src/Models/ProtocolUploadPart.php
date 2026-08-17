<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

final readonly class ProtocolUploadPart extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            Payload::string($raw, 'id'),
            Payload::string($raw, 'object'),
            Payload::int($raw, 'created_at'),
            Payload::string($raw, 'upload_id'),
            $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $id,
        public string $object,
        public int $createdAt,
        public string $uploadId,
        array $raw,
    ) {
        parent::__construct($raw);
    }
}
