<?php

declare(strict_types=1);

namespace RunApi\Core\Http;

/** Exact successful HTTP response for endpoints that can return JSON or raw text. */
final readonly class RawResponse
{
    /** @var array<string, list<string>> */
    public array $headers;

    /** @param array<array-key, mixed> $headers */
    public function __construct(
        public int $statusCode,
        public string $body,
        public ?string $contentType,
        array $headers = [],
    ) {
        $this->headers = $this->normalizeHeaders($headers);
    }

    public function header(string $name): ?string
    {
        foreach ($this->headers as $headerName => $values) {
            if (strcasecmp($headerName, $name) === 0) {
                return $values[0] ?? null;
            }
        }

        return null;
    }

    /** @param array<array-key, mixed> $headers
     * @return array<string, list<string>>
     */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $values) {
            if (!is_string($name)) {
                throw new \InvalidArgumentException('response header names must be strings');
            }
            if (is_string($values)) {
                $normalized[$name] = [$values];
                continue;
            }
            if (!is_array($values)) {
                throw new \InvalidArgumentException('response header values must be strings or lists of strings');
            }

            $normalizedValues = array_values($values);
            foreach ($normalizedValues as $value) {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException('response header values must be strings or lists of strings');
                }
            }
            $normalized[$name] = $normalizedValues;
        }

        return $normalized;
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
