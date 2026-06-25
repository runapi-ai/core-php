<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

/**
 * Base value object that preserves the raw RunAPI response payload.
 */
abstract readonly class BaseModel
{
    /**
     * Create a base model value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(private array $raw = [])
    {
    }

    /**
     * Return the raw RunAPI payload preserved by this model.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }
}
