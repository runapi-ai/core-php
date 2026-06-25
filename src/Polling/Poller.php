<?php

declare(strict_types=1);

namespace RunApi\Core\Polling;

use Closure;
use RunApi\Core\Constants;
use RunApi\Core\Errors\TaskFailedException;
use RunApi\Core\Errors\TaskTimeoutException;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\RequestOptions;

/**
 * Polls async task status until completion, failure, or timeout.
 */
final class Poller
{
    private Closure $sleep;
    private Closure $now;

    /**
     * Create a poller with injectable sleep and clock callbacks for tests.
     */
    public function __construct(?callable $sleep = null, ?callable $now = null)
    {
        $this->sleep = Closure::fromCallable($sleep ?? static function (float $seconds): void {
            usleep((int) round($seconds * 1_000_000));
        });
        $this->now = Closure::fromCallable($now ?? static fn (): float => microtime(true));
    }

    /**
     * Poll until the task completes, fails, or exceeds the configured maximum wait time.
     *
     * @template T of TaskResponse
     *
     * @param callable(): T $fetcher
     *
     * @return T
     */
    public function untilComplete(callable $fetcher, ?RequestOptions $options = null): TaskResponse
    {
        $pollInterval = $options === null ? Constants::DEFAULT_POLL_INTERVAL_SECONDS : ($options->pollIntervalSeconds ?? Constants::DEFAULT_POLL_INTERVAL_SECONDS);
        $maxWait = $options === null ? Constants::DEFAULT_MAX_WAIT_SECONDS : ($options->maxWaitSeconds ?? Constants::DEFAULT_MAX_WAIT_SECONDS);
        $start = ($this->now)();
        $lastResponse = null;

        while (true) {
            $response = $fetcher();
            $lastResponse = $response;
            $status = $this->normalizeStatus($response->status);

            if ($status === 'completed') {
                return $response;
            }

            if ($status === 'failed') {
                throw new TaskFailedException($response->error ?? 'Task failed', details: $response->toArray());
            }

            if ((($this->now)() - $start) >= $maxWait) {
                throw new TaskTimeoutException('Task polling timed out', details: $lastResponse->toArray());
            }

            ($this->sleep)($pollInterval);
        }
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower($status)) {
            'completed' => 'completed',
            'failed' => 'failed',
            default => 'processing',
        };
    }
}
