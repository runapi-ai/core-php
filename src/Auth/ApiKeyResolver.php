<?php

declare(strict_types=1);

namespace RunApi\Core\Auth;

use RunApi\Core\Errors\AuthenticationException;

/**
 * Resolves the API key from explicit options or the RUNAPI_API_KEY environment variable.
 */
final class ApiKeyResolver
{
    public const ENV_VAR_NAME = 'RUNAPI_API_KEY';

    /**
     * Resolve the API key or raise an authentication exception when none is available.
     */
    public static function resolve(?string $explicit): string
    {
        $apiKey = self::normalize($explicit) ?? self::normalize(getenv(self::ENV_VAR_NAME) ?: null);

        if ($apiKey === null) {
            throw new AuthenticationException(
                'API key is required. Pass apiKey or set the RUNAPI_API_KEY environment variable.',
            );
        }

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
