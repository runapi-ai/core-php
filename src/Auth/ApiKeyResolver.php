<?php

declare(strict_types=1);

namespace RunApi\Core\Auth;

/**
 * Resolves the API key from explicit options or the RUNAPI_API_KEY environment variable.
 */
final class ApiKeyResolver
{
    public const ENV_VAR_NAME = 'RUNAPI_API_KEY';

    /**
     * Resolve the optional API key from explicit options or the environment.
     */
    public static function resolve(?string $explicit): ?string
    {
        $apiKey = self::normalize($explicit) ?? self::normalize(getenv(self::ENV_VAR_NAME) ?: null);

        return $apiKey;
    }

    private static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
