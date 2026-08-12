<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\Errors\ErrorMapper;
use RunApi\Core\Errors\ValidationException;

final class ErrorMapperTest extends TestCase
{
    public function testDoesNotUseLegacyErrorsArrayAsMessage(): void
    {
        $error = ErrorMapper::fromResponse(new Response(400), '{"errors":["First error"]}');

        self::assertSame('RunAPI request failed with status 400', $error->getMessage());
    }

    public function testKeepsResourceValidationSummaryAndDetails(): void
    {
        $body = '{"error":"Validation failed","errors":{"prompt":["is required"]}}';

        $error = ErrorMapper::fromResponse(new Response(422), $body);

        self::assertInstanceOf(ValidationException::class, $error);
        self::assertSame('Validation failed', $error->getMessage());
        self::assertSame(['prompt' => ['is required']], $error->details['errors']);
        self::assertSame($body, $error->responseBody);
    }
}
