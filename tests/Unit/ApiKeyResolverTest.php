<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Auth\ApiKeyResolver;
use RunApi\Core\Errors\AuthenticationException;

final class ApiKeyResolverTest extends TestCase
{
    public function testExplicitApiKeyWinsOverEnvironment(): void
    {
        $this->withEnv('RUNAPI_API_KEY', 'env-key', function (): void {
            self::assertSame('explicit-key', ApiKeyResolver::resolve(' explicit-key '));
        });
    }

    public function testFallsBackToEnvironment(): void
    {
        $this->withEnv('RUNAPI_API_KEY', ' env-key ', function (): void {
            self::assertSame('env-key', ApiKeyResolver::resolve(null));
        });
    }

    public function testRaisesAuthenticationExceptionWhenMissing(): void
    {
        $this->withEnv('RUNAPI_API_KEY', null, function (): void {
            $this->expectException(AuthenticationException::class);
            $this->expectExceptionMessage('API key is required');

            ApiKeyResolver::resolve('   ');
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
