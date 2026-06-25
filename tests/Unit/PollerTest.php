<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Errors\TaskFailedException;
use RunApi\Core\Errors\TaskTimeoutException;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Polling\Poller;
use RunApi\Core\RequestOptions;

final class PollerTest extends TestCase
{
    public function testReturnsCompletedResponse(): void
    {
        $sleeps = [];
        $poller = new Poller(
            sleep: static function (float $seconds) use (&$sleeps): void {
                $sleeps[] = $seconds;
            },
            now: static fn (): float => 0.0,
        );
        $responses = [
            new TaskResponse('task_123', 'processing'),
            new TaskResponse('task_123', 'completed', raw: ['id' => 'task_123', 'status' => 'completed']),
        ];

        $result = $poller->untilComplete(
            fetcher: static function () use (&$responses): TaskResponse {
                return array_shift($responses);
            },
            options: new RequestOptions(pollIntervalSeconds: 0.25),
        );

        self::assertSame('completed', $result->status);
        self::assertSame([0.25], $sleeps);
    }

    public function testRaisesTaskFailedException(): void
    {
        $poller = new Poller(sleep: static fn (): null => null, now: static fn (): float => 0.0);

        try {
            $poller->untilComplete(static fn (): TaskResponse => new TaskResponse(
                id: 'task_123',
                status: 'failed',
                error: 'render failed',
                raw: ['id' => 'task_123', 'status' => 'failed', 'error' => 'render failed'],
            ));
            self::fail('Expected a task failed exception.');
        } catch (TaskFailedException $exception) {
            self::assertSame('render failed', $exception->getMessage());
            self::assertSame(['id' => 'task_123', 'status' => 'failed', 'error' => 'render failed'], $exception->details);
        }
    }

    public function testRaisesTaskTimeoutException(): void
    {
        $times = [0.0, 2.0];
        $poller = new Poller(
            sleep: static fn (): null => null,
            now: static function () use (&$times): float {
                return array_shift($times) ?? 2.0;
            },
        );

        try {
            $poller->untilComplete(
                fetcher: static fn (): TaskResponse => new TaskResponse(
                    id: 'task_123',
                    status: 'processing',
                    raw: ['id' => 'task_123', 'status' => 'processing'],
                ),
                options: new RequestOptions(maxWaitSeconds: 1.0),
            );
            self::fail('Expected a task timeout exception.');
        } catch (TaskTimeoutException $exception) {
            self::assertSame('Task polling timed out', $exception->getMessage());
            self::assertSame(['id' => 'task_123', 'status' => 'processing'], $exception->details);
        }
    }
}
