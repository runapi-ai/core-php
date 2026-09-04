<?php

declare(strict_types=1);

namespace RunApi\Core\Tasks;

use Generator;
use RunApi\Core\Constants;
use RunApi\Core\Errors\TaskTimeoutException;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Http\RawResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Support\Json;

/** Follows an opaque Task Result URL until a terminal response is available. */
final readonly class HybridTaskSubscriber
{
    public function __construct(private HttpClient $http)
    {
    }

    /** @return Generator<int, HybridTaskUpdate> */
    public function subscribe(HybridTask $task, ?RequestOptions $options = null): Generator
    {
        $startedAt = microtime(true);
        $maxWaitSeconds = $options === null ? Constants::DEFAULT_MAX_WAIT_SECONDS : ($options->maxWaitSeconds ?? Constants::DEFAULT_MAX_WAIT_SECONDS);
        $lastResponse = null;

        while (true) {
            if ($lastResponse !== null && (microtime(true) - $startedAt) >= $maxWaitSeconds) {
                throw new TaskTimeoutException('Task polling timed out', details: $lastResponse->value());
            }
            $response = $this->http->requestRaw('GET', $task->location, ['options' => $options]);
            $lastResponse = $response;
            $payload = Json::decodeObject($response->body);
            $status = $payload['status'] ?? null;
            if (!is_string($status)) {
                throw new ValidationException('Task Result must include status');
            }

            if ($status === 'processing') {
                yield new HybridTaskUpdate($task, $status, $response);
                $remainingWaitSeconds = $maxWaitSeconds - (microtime(true) - $startedAt);
                if ($remainingWaitSeconds <= 0.0) {
                    throw new TaskTimeoutException('Task polling timed out', details: $response->value());
                }
                $retryAfterSeconds = HybridTask::retryAfter($response) ?? $task->retryAfterSeconds ?? ($options === null ? Constants::DEFAULT_POLL_INTERVAL_SECONDS : ($options->pollIntervalSeconds ?? Constants::DEFAULT_POLL_INTERVAL_SECONDS));
                $this->sleep(min($retryAfterSeconds, $remainingWaitSeconds));
                continue;
            }

            if ($status === 'completed' || $status === 'failed') {
                $terminal = $this->terminalResponse($payload);
                $error = $status === 'failed' ? $this->error($terminal) : null;
                yield new HybridTaskUpdate($task, $status, $terminal, $error);

                return;
            }

            throw new ValidationException('Task Result returned an unsupported status');
        }
    }

    /** @param array<string, mixed> $payload */
    private function terminalResponse(array $payload): RawResponse
    {
        $response = $payload['response'] ?? null;
        if (!is_array($response) || !is_int($response['status'] ?? null) || !is_string($response['content_type'] ?? null)) {
            throw new ValidationException('Terminal Task Result must include response status and content_type');
        }

        $body = $response['body'] ?? null;
        $contentType = $response['content_type'];
        $encodedBody = $this->encodeBody($body, $contentType);
        $headers = $response['headers'] ?? [];
        if (!is_array($headers)) {
            throw new ValidationException('Terminal Task Result headers must be an object');
        }

        /** @var array<string, list<string>> $headers */
        return new RawResponse($response['status'], $encodedBody, $contentType, $headers);
    }

    private function encodeBody(mixed $body, string $contentType): string
    {
        $mediaType = strtolower(trim(explode(';', $contentType, 2)[0]));
        if ($mediaType === 'application/json' || str_ends_with($mediaType, '+json')) {
            return Json::encode($body);
        }

        if (!is_string($body)) {
            throw new ValidationException('Non-JSON Task Result body must be a string');
        }

        return $body;
    }

    private function error(RawResponse $response): string
    {
        $value = $response->value();
        if (is_array($value) && is_string($value['error'] ?? null) && $value['error'] !== '') {
            return $value['error'];
        }

        return 'Task failed';
    }

    private function sleep(float $seconds): void
    {
        if ($seconds > 0.0) {
            usleep((int) round($seconds * 1_000_000));
        }
    }
}
