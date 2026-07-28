<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\Models\TaskRefund;
use RunApi\Core\Models\TaskReservation;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Models\TaskSettlement;

final class TaskResponseTest extends TestCase
{
    public function testBuildsDefaultArrayPayload(): void
    {
        $response = new TaskResponse('task_123', 'processing');

        self::assertSame('task_123', $response->id);
        self::assertSame('processing', $response->status);
        self::assertSame(['id' => 'task_123', 'status' => 'processing', 'error' => null, 'billing' => null], $response->toArray());
    }

    public function testPreservesRawPayload(): void
    {
        $raw = ['id' => 'task_123', 'status' => 'completed', 'output_url' => 'https://file.runapi.ai/video.mp4'];
        $response = new TaskResponse('task_123', 'completed', raw: $raw);

        self::assertSame($raw, $response->toArray());
    }

    public function testHydratesTypedBillingFacts(): void
    {
        $raw = ['id' => 'task_123', 'status' => 'completed', 'billing' => ['reservation' => ['amount_cents' => 12], 'settlement' => ['charged_amount_cents' => 11, 'amount_micro_cents' => 1050000], 'refund' => ['refunded_at' => '2026-07-23T12:00:00.000000Z']]];
        $response = new TaskResponse('task_123', 'completed', raw: $raw);

        $billing = $response->billing;
        self::assertNotNull($billing);
        self::assertNotNull($billing->reservation);
        self::assertNotNull($billing->settlement);
        self::assertNotNull($billing->refund);
        self::assertSame(12, $billing->reservation->amountCents);
        self::assertSame(11, $billing->settlement->chargedAmountCents);
        self::assertSame(1050000, $billing->settlement->amountMicroCents);
        self::assertSame('2026-07-23T12:00:00.000000Z', $billing->refund->refundedAt);
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
    public function testAutoloadsPublicBillingFactTypes(): void
    {
        self::assertTrue(class_exists(TaskReservation::class));
        self::assertTrue(class_exists(TaskSettlement::class));
        self::assertTrue(class_exists(TaskRefund::class));
    }
}
