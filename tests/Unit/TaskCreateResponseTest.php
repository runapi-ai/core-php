<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Models\TaskCreateResponse;

final class TaskCreateResponseTest extends TestCase
{
    public function testRetainsTheLegacyPositionalRawPayloadArgument(): void
    {
        $response = new TaskCreateResponse('task_1', ['id' => 'task_1', 'custom' => 'kept']);

        self::assertSame(['id' => 'task_1', 'custom' => 'kept'], $response->toArray());
        self::assertNull($response->taskReplayed);
    }

    public function testHydratesOptionalReplayState(): void
    {
        $response = TaskCreateResponse::fromArray(['id' => 'task_1', 'task_replayed' => true]);

        self::assertTrue($response->taskReplayed);
    }
}
