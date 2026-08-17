<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Support\Payload;

final readonly class ProtocolFileList extends BaseModel
{
    /**
     * @param list<ProtocolFile> $data
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $object,
        public array $data,
        public ?string $firstId,
        public ?string $lastId,
        public bool $hasMore,
        array $raw,
    ) {
        parent::__construct($raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        if (!isset($raw['has_more']) || !is_bool($raw['has_more'])) {
            throw new ValidationException('has_more must be a boolean');
        }

        return new self(
            object: Payload::string($raw, 'object'),
            data: Payload::listOf($raw, 'data', ProtocolFile::fromArray(...), required: true),
            firstId: Payload::optionalString($raw, 'first_id'),
            lastId: Payload::optionalString($raw, 'last_id'),
            hasMore: $raw['has_more'],
            raw: $raw,
        );
    }
}
