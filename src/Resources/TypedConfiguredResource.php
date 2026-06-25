<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\RequestOptions;

/**
 * Typed configured resource operations for Core.
 */
readonly class TypedConfiguredResource extends ConfiguredAsyncResource
{
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     *
     * @param class-string<TaskResponse> $expectedResponseClass
     * @param class-string<TaskResponse> $expectedCompletedResponseClass
     * @param list<string> $models
     */
    public function __construct(
        HttpClient $http,
        string $endpoint,
        string $action,
        string $responseClass,
        string $completedResponseClass,
        array $models = [],
        private string $resourceName = 'resource',
        private string $expectedResponseClass = TaskResponse::class,
        private string $expectedCompletedResponseClass = TaskResponse::class,
    ) {
        parent::__construct($http, $endpoint, $action, $responseClass, $completedResponseClass, $models);
    }

    /**
     * Fetch the current status of a typed configured resource task.
     */
    public function get(string $id, ?RequestOptions $options = null): TaskResponse
    {
        $response = parent::get($id, $options);
        if (!$response instanceof $this->expectedResponseClass) {
            throw new ValidationException($this->resourceName . ' status returned an invalid response');
        }

        return $response;
    }

    /**
     * Submit a typed configured resource task and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): TaskResponse
    {
        $response = parent::run($params, $options);
        if (!$response instanceof $this->expectedCompletedResponseClass) {
            throw new ValidationException($this->resourceName . ' polling returned an invalid response');
        }

        return $response;
    }
}
