<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Constants;

final class ClientOptionsTest extends TestCase
{
    public function testResolvedBaseUrlTrimsTrailingSlash(): void
    {
        $options = new ClientOptions(baseUrl: ' https://runapi.ai/ ');

        self::assertSame('https://runapi.ai', $options->resolvedBaseUrl());
    }

    public function testResolvedBaseUrlFallsBackToEnvironment(): void
    {
        $this->withEnv(Constants::BASE_URL_ENV_VAR, ' https://env.runapi.ai/v1/ ', function (): void {
            self::assertSame('https://env.runapi.ai/v1', (new ClientOptions())->resolvedBaseUrl());
        });
    }

    public function testResolvedBaseUrlFallsBackToDefault(): void
    {
        $this->withEnv(Constants::BASE_URL_ENV_VAR, null, function (): void {
            self::assertSame(Constants::DEFAULT_BASE_URL, (new ClientOptions())->resolvedBaseUrl());
        });
    }

    private function withEnv(string $name, ?string $value, callable $callback): void
    {
        $previous = getenv($name);
        $hadPrevious = $previous !== false;

        try {
            $value === null ? putenv($name) : putenv($name . '=' . $value);
            $callback();
        } finally {
            $hadPrevious ? putenv($name . '=' . $previous) : putenv($name);
        }
    }
}
