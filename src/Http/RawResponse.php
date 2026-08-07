<?php

declare(strict_types=1);

namespace RunApi\Core\Http;

/** Exact successful HTTP response for endpoints that can return JSON or raw text. */
final readonly class RawResponse
{
    public function __construct(
        public int $statusCode,
        public string $body,
        public ?string $contentType,
    ) {
    }

    /** @return array<string, mixed>|string */
    public function value(): array|string
    {
        $contentType = strtolower(trim(explode(';', $this->contentType ?? '', 2)[0]));
        if ($contentType !== 'application/json' && !str_ends_with($contentType, '+json')) {
            return $this->body;
        }

        try {
            $decoded = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->body;
        }

        return is_array($decoded) && !array_is_list($decoded) ? $decoded : $this->body;
    }
}
