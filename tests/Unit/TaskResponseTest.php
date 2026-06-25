<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Models\TaskResponse;

final class TaskResponseTest extends TestCase
{
    public function testBuildsDefaultArrayPayload(): void
    {
        $response = new TaskResponse('task_123', 'processing');

        self::assertSame('task_123', $response->id);
        self::assertSame('processing', $response->status);
        self::assertSame(['id' => 'task_123', 'status' => 'processing', 'error' => null], $response->toArray());
    }

    public function testPreservesRawPayload(): void
    {
        $raw = ['id' => 'task_123', 'status' => 'completed', 'output_url' => 'https://file.runapi.ai/video.mp4'];
        $response = new TaskResponse('task_123', 'completed', raw: $raw);

        self::assertSame($raw, $response->toArray());
    }
}
