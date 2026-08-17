<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Support\Payload;

final readonly class DeletedProtocolFile extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        if (!isset($raw['deleted']) || !is_bool($raw['deleted'])) {
            throw new ValidationException('deleted must be a boolean');
        }
        return new self(Payload::string($raw, 'id'), Payload::string($raw, 'object'), $raw['deleted'], $raw);
    }

    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $id,
        public string $object,
        public bool $deleted,
        array $raw,
    ) {
        parent::__construct($raw);
    }
}
