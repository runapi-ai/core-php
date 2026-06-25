<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Contract\ContractValidator;
use RunApi\Core\Errors\ValidationException;
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
        protected ContractValidator $validator = new ContractValidator(),
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
        $params = $this->compact($params);
        $model = $this->model($params);
        $this->validator->validate($this->action(), $model, $params);
        $this->validate($params, $model);

        return TaskCreateResponse::fromArray($this->http->request('post', $this->endpoint(), [
            'body' => $params,
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

    abstract protected function action(): string;

    /**
     * @param array<string, mixed> $raw
     */
    abstract protected function hydrate(array $raw): TaskResponse;

    abstract protected function hydrateCompleted(TaskResponse $response): TaskResponse;

    /**
     * @param array<string, mixed> $params
     */
    protected function model(array $params): string
    {
        $model = $params['model'] ?? null;
        if ($model === null || $model === '') {
            return '_';
        }

        if (!is_string($model)) {
            throw new ValidationException('model must be a string');
        }

        return $model;
    }

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

    /**
     * @param list<string> $allowed
     */
    protected function validateModel(string $model, array $allowed): void
    {
        if (!in_array($model, $allowed, true)) {
            throw new ValidationException('model must be one of the allowed values');
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function requireField(array $params, string $field): void
    {
        if (!array_key_exists($field, $params) || $params[$field] === null || $params[$field] === '') {
            throw new ValidationException($field . ' is required');
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function validate(array $params, string $model): void
    {
    }
}
