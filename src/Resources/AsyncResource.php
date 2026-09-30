<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Polling\Poller;
use RunApi\Core\RequestOptions;

/**
 * Async resource operations for Core.
 */
abstract readonly class AsyncResource
{
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     */
    public function __construct(
        protected HttpClient $http,
        protected Poller $poller = new Poller(),
    ) {
    }

    /**
     * Create an async resource task and return immediately with a task id.
     *
     * @param array<string, mixed> $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return TaskCreateResponse::fromArray($this->http->request('post', $this->endpoint(), [
            'body' => $this->compact($params),
            'options' => $options,
        ]));
    }

    /**
     * Fetch the current status of an async resource task.
     */
    public function get(string $id, ?RequestOptions $options = null): TaskResponse
    {
        return $this->hydrate($this->http->request('get', $this->endpoint() . '/' . rawurlencode($id), [
            'options' => $options,
        ]));
    }

    /**
     * Submit the async resource request and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): TaskResponse
    {
        $task = $this->create($params, $options);
        $response = $this->poller->untilComplete(fn (): TaskResponse => $this->get($task->id, $options), $options);

        return $this->hydrateCompleted($response);
    }

    abstract protected function endpoint(): string;


    /**
     * @param array<string, mixed> $raw
     */
    abstract protected function hydrate(array $raw): TaskResponse;

    abstract protected function hydrateCompleted(TaskResponse $response): TaskResponse;

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    protected function compact(array $params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value) && $value === []) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
