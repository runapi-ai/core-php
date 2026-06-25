<?php

declare(strict_types=1);

namespace RunApi\Core\Support;

use JsonException;
use RunApi\Core\Errors\ValidationException;

/**
 * JSON encoding and decoding helpers used by the SDK transport.
 */
final class Json
{
    /**
     * Encode a value as JSON for an HTTP request body.
     */
    public static function encode(mixed $value): string
    {
        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ValidationException('Request body must be valid JSON', previous: $exception);
        }
    }

    /**
     * Decode a JSON object response body.
     *
     * @return array<string, mixed>
     */
    public static function decodeObject(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        try {
            $decoded = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ValidationException('Response body must be valid JSON', previous: $exception);
        }

        if (!is_array($decoded)) {
            throw new ValidationException('Response body must be a JSON object');
        }

        return $decoded;
    }
}
