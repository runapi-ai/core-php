<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\RequestOptions;

/**
 * Configured async resource operations for Core.
 */
readonly class ConfiguredAsyncResource extends AsyncResource
{
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     *
     * @param class-string<TaskResponse> $responseClass
     * @param class-string<TaskResponse> $completedResponseClass
     */
    public function __construct(
        HttpClient $http,
        private string $endpoint,
        private string $responseClass,
        private string $completedResponseClass,
    ) {
        parent::__construct($http);
    }

    /**
     * Create a configured async resource task and return immediately with a task id.
     *
     * @param array<string, mixed> $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    protected function endpoint(): string
    {
        return $this->endpoint;
    }


    /**
     * @param array<string, mixed> $raw
     */
    protected function hydrate(array $raw): TaskResponse
    {
        $factory = [$this->responseClass, 'fromArray'];
        if (!is_callable($factory)) {
            throw new ValidationException($this->responseClass . ' must define fromArray');
        }

        $response = $factory($raw);
        if (!$response instanceof TaskResponse) {
            throw new ValidationException($this->responseClass . ' must return a TaskResponse');
        }

        return $response;
    }

    protected function hydrateCompleted(TaskResponse $response): TaskResponse
    {
        $factory = [$this->completedResponseClass, 'fromResponse'];
        if (!is_callable($factory)) {
            throw new ValidationException($this->completedResponseClass . ' must define fromResponse');
        }

        $completed = $factory($response);
        if (!$completed instanceof TaskResponse) {
            throw new ValidationException($this->completedResponseClass . ' must return a TaskResponse');
        }

        return $completed;
    }
}
