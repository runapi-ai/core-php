<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use Generator;
use RunApi\Core\Contract\ContractValidator;
use RunApi\Core\Errors\TaskFailedException;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Http\RawResponse;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\RequestOptions;
use RunApi\Core\Support\Json;
use RunApi\Core\Tasks\HybridTask;
use RunApi\Core\Tasks\HybridTaskSubscriber;
use RunApi\Core\Tasks\HybridTaskUpdate;

/** Shared terminal-or-accepted Task lifecycle for model resources. */
abstract readonly class HybridResource
{
    /** @param class-string<BaseModel> $responseClass */
    public function __construct(
        protected HttpClient $http,
        private string $endpoint,
        private string $action,
        private string $responseClass,
        private ContractValidator $validator = new ContractValidator(),
    ) {
    }

    /** @param array<string, mixed> $params */
    public function run(array $params, ?RequestOptions $options = null): BaseModel
    {
        foreach ($this->subscribe($params, $options) as $update) {
            if ($update->status === 'completed') {
                return $this->hydrate($update->response);
            }
            if ($update->status === 'failed') {
                throw new TaskFailedException($update->error ?? 'Task failed', details: $update->response->value());
            }
        }

        throw new ValidationException('Hybrid Task did not return a terminal response');
    }

    /** @param array<string, mixed> $params
     * @return Generator<int, HybridTaskUpdate>
     */
    public function subscribe(array $params, ?RequestOptions $options = null): Generator
    {
        yield from $this->subscribeRequest($params, $options);
    }

    /** @param array<string, mixed> $params
     * @return Generator<int, HybridTaskUpdate>
     */
    protected function subscribeRequest(
        array $params,
        ?RequestOptions $options = null,
        string $method = 'POST',
        ?string $path = null,
        string $placement = 'body',
    ): Generator {
        $response = $this->executeRaw($params, $options, $method, $path, $placement);
        if ($response->statusCode !== 202) {
            $task = new HybridTask('', '', null);
            yield new HybridTaskUpdate($task, 'completed', $response);

            return;
        }

        yield from (new HybridTaskSubscriber($this->http))->subscribe(HybridTask::fromAcceptance($response), $options);
    }

    /** @param array<string, mixed> $params */
    protected function execute(array $params, ?RequestOptions $options = null, string $method = 'POST', ?string $path = null, string $placement = 'body'): BaseModel
    {
        foreach ($this->subscribeRequest($params, $options, $method, $path, $placement) as $update) {
            if ($update->status === 'completed') {
                return $this->hydrate($update->response);
            }
            if ($update->status === 'failed') {
                throw new TaskFailedException($update->error ?? 'Task failed', details: $update->response->value());
            }
        }

        throw new ValidationException('Hybrid Task did not return a terminal response');
    }

    /** @param array<string, mixed> $params */
    private function executeRaw(array $params, ?RequestOptions $options, string $method = 'POST', ?string $path = null, string $placement = 'body'): RawResponse
    {
        $params = $this->compact($params);
        $model = $params['model'] ?? '_';
        if (!is_string($model)) {
            throw new ValidationException('model must be a string');
        }
        $this->validator->validate($this->action, $model, $params);

        $request = ['options' => $options, 'accepted_statuses' => [202]];
        if ($placement === 'body') {
            $request['body'] = $params;
        } elseif ($placement === 'query') {
            $request['query'] = $params;
        }
        if (strtoupper($method) === 'POST' && !($options?->hasHeader('Idempotency-Key') ?? false)) {
            $request['headers'] = ['Idempotency-Key' => bin2hex(random_bytes(16))];
        }

        return $this->http->requestRaw($method, $path ?? $this->endpoint, $request);
    }

    private function hydrate(RawResponse $response): BaseModel
    {
        $value = $response->value();
        if ($response->contentType === null) {
            $value = Json::decodeObject($response->body);
        }
        if (!is_array($value)) {
            throw new ValidationException($this->responseClass . ' requires a JSON object response');
        }
        $factory = [$this->responseClass, 'fromArray'];
        if (!is_callable($factory)) {
            throw new ValidationException($this->responseClass . ' must define fromArray');
        }

        $model = $factory($value);
        if (!$model instanceof BaseModel) {
            throw new ValidationException($this->responseClass . ' must return a BaseModel');
        }

        return $model;
    }

    /** @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function compact(array $params): array
    {
        return array_filter($params, static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);
    }
}
