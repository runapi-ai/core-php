<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Models\TaskUsage;

final class TaskResponseTest extends TestCase
{
    public function testBuildsDefaultArrayPayload(): void
    {
        $response = new TaskResponse('task_123', 'processing');

        self::assertSame('task_123', $response->id);
        self::assertSame('processing', $response->status);
        self::assertSame(['id' => 'task_123', 'status' => 'processing', 'error' => null, 'usage' => null], $response->toArray());
    }

    public function testPreservesRawPayload(): void
    {
        $raw = ['id' => 'task_123', 'status' => 'completed', 'output_url' => 'https://file.runapi.ai/video.mp4'];
        $response = new TaskResponse('task_123', 'completed', raw: $raw);

        self::assertSame($raw, $response->toArray());
    }

    public function testHydratesTypedUsageCost(): void
    {
        $raw = ['id' => 'task_123', 'status' => 'completed', 'usage' => ['cost' => 0.05]];
        $response = new TaskResponse('task_123', 'completed', raw: $raw);

        self::assertInstanceOf(TaskUsage::class, $response->usage);
        self::assertSame(0.05, $response->usage->cost);
    }

    public function testKeepsRawPayloadInTheExistingPositionalArgumentSlots(): void
    {
        $creation = new TaskCreateResponse('task_123', ['id' => 'task_123']);
        $response = new TaskResponse('task_123', 'processing', null, ['id' => 'task_123', 'status' => 'processing']);

        self::assertSame(['id' => 'task_123'], $creation->toArray());
        self::assertSame(['id' => 'task_123', 'status' => 'processing'], $response->toArray());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAutoloadsPublicUsageType(): void
    {
        self::assertTrue(class_exists(TaskUsage::class));
    }
}
