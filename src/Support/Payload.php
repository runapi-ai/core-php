<?php

declare(strict_types=1);

namespace RunApi\Core\Support;

use RunApi\Core\Errors\ValidationException;

/**
 * Typed payload extraction helpers used by response models.
 */
final class Payload
{
    /**
     * Read a required string field from a response payload.
     *
     * @param array<string, mixed> $payload
     */
    public static function string(array $payload, string $key): string
    {
        $value = $payload[$key] ?? null;
        if (!is_string($value)) {
            throw new ValidationException($key . ' must be a string');
        }

        return $value;
    }

    /**
     * Read a required int field from a response payload.
     *
     * @param array<string, mixed> $payload
     */
    public static function int(array $payload, string $key): int
    {
        $value = $payload[$key] ?? null;
        if (!is_int($value)) {
            throw new ValidationException($key . ' must be an integer');
        }

        return $value;
    }

    /**
     * Read a required array field from a response payload.
     *
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public static function array(array $payload, string $key): array
    {
        $value = $payload[$key] ?? null;
        if (!is_array($value)) {
            throw new ValidationException($key . ' must be an object');
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * Read an optional string field: null when absent, validated when present.
     *
     * @param array<string, mixed> $payload
     */
    public static function optionalString(array $payload, string $key): ?string
    {
        $value = $payload[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new ValidationException($key . ' must be a string');
        }

        return $value;
    }

    /**
     * Hydrate a list of objects from a response payload, validating shape.
     *
     * @template T
     *
     * @param array<string, mixed> $payload
     * @param callable(array<string, mixed>): T $factory
     *
     * @return list<T>
     */
    public static function listOf(array $payload, string $key, callable $factory, bool $required = false): array
    {
        $value = $payload[$key] ?? null;
        if ($value === null) {
            if ($required) {
                throw new ValidationException($key . ' is required');
            }

            return [];
        }
        if (!is_array($value)) {
            throw new ValidationException($key . ' must be an array');
        }

        $items = [];
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                throw new ValidationException($key . '[' . $index . '] must be an object');
            }
            /** @var array<string, mixed> $item */
            $items[] = $factory($item);
        }

        return $items;
    }

    private function __construct()
    {
    }
}
