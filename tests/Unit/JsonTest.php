<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Support\Json;

final class JsonTest extends TestCase
{
    public function testEncodesJson(): void
    {
        self::assertSame('{"prompt":"hello"}', Json::encode(['prompt' => 'hello']));
    }

    public function testDecodesJsonObject(): void
    {
        self::assertSame(['id' => 'task_123'], Json::decodeObject('{"id":"task_123"}'));
    }

    public function testDecodesEmptyBodyAsEmptyObject(): void
    {
        self::assertSame([], Json::decodeObject(''));
    }

    public function testRaisesValidationExceptionForInvalidJson(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Response body must be valid JSON');

        Json::decodeObject('{');
    }
}
