<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Support\Payload;

final class PayloadTest extends TestCase
{
    public function testOptionalStringReturnsNullWhenAbsent(): void
    {
        self::assertNull(Payload::optionalString([], 'error'));
        self::assertNull(Payload::optionalString(['error' => null], 'error'));
    }

    public function testOptionalStringReturnsValueWhenPresent(): void
    {
        self::assertSame('boom', Payload::optionalString(['error' => 'boom'], 'error'));
    }

    public function testOptionalStringRejectsNonString(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('error must be a string');

        Payload::optionalString(['error' => 5], 'error');
    }

    public function testListOfHydratesEachItem(): void
    {
        $items = Payload::listOf(
            ['images' => [['url' => 'a'], ['url' => 'b']]],
            'images',
            static fn (array $item): string => (string) $item['url'],
        );

        self::assertSame(['a', 'b'], $items);
    }

    public function testListOfReturnsEmptyWhenAbsentAndOptional(): void
    {
        self::assertSame([], Payload::listOf([], 'images', static fn (array $item): array => $item));
    }

    public function testListOfThrowsWhenAbsentAndRequired(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images is required');

        Payload::listOf([], 'images', static fn (array $item): array => $item, required: true);
    }

    public function testListOfRejectsNonArrayValue(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images must be an array');

        Payload::listOf(['images' => 'nope'], 'images', static fn (array $item): array => $item);
    }

    public function testListOfRejectsNonObjectItem(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images[1] must be an object');

        Payload::listOf(['images' => [['url' => 'a'], 'bad']], 'images', static fn (array $item): array => $item);
    }
}
