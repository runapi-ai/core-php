<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\NetworkException;
use RunApi\Core\Errors\RateLimitException;
use RunApi\Core\Errors\RunApiException;
use RunApi\Core\Errors\ServerException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Core\Tests\Fixtures\FakeClientException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class HttpClientTest extends TestCase
{
    public function testUsesDefaultGuzzleClientAndPsrFactories(): void
    {
        $client = new HttpClient(new ClientOptions(apiKey: 'test-key'));

        self::assertInstanceOf(GuzzleClient::class, $this->privateProperty($client, 'client'));
        self::assertInstanceOf(HttpFactory::class, $this->privateProperty($client, 'requestFactory'));
        self::assertInstanceOf(HttpFactory::class, $this->privateProperty($client, 'streamFactory'));
    }

    public function testSendsJsonRequestWithAuthHeadersAndQueryParams(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"id":"task_123","ok":true}'),
        ]);
        $client = new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            baseUrl: 'https://api.runapi.ai/base/',
            httpClient: $transport,
            maxRetries: 0,
            headers: ['X-Client' => 'base'],
            userAgent: 'runapi-test',
        ));

        $result = $client->request('post', '/api/v1/tasks', [
            'query' => ['model' => 'kling', 'skip' => null],
            'headers' => ['X-Request' => 'request'],
            'body' => ['prompt' => 'hello'],
        ]);

        self::assertSame(['id' => 'task_123', 'ok' => true], $result);
        self::assertCount(1, $transport->requests);

        $request = $transport->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://api.runapi.ai/base/api/v1/tasks?model=kling', (string) $request->getUri());
        self::assertSame('Bearer test-key', $request->getHeaderLine('Authorization'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame('identity', $request->getHeaderLine('Accept-Encoding'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('runapi-test', $request->getHeaderLine('User-Agent'));
        self::assertSame('base', $request->getHeaderLine('X-Client'));
        self::assertSame('request', $request->getHeaderLine('X-Request'));
        self::assertSame('{"prompt":"hello"}', (string) $request->getBody());
    }

    public function testMapsErrorResponsesToTypedExceptions(): void
    {
        $transport = new QueueHttpClient([
            new Response(
                429,
                ['Retry-After' => '2', 'X-Request-ID' => 'req_123'],
                '{"error":{"message":"slow down","code":"rate_limit"}}',
            ),
        ]);
        $client = new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            httpClient: $transport,
            maxRetries: 0,
        ));

        try {
            $client->request('get', '/api/v1/tasks/task_123');
            self::fail('Expected a rate limit exception.');
        } catch (RateLimitException $exception) {
            self::assertSame('slow down', $exception->getMessage());
            self::assertSame(429, $exception->statusCode);
            self::assertSame('req_123', $exception->requestId);
            self::assertSame('rate_limit', $exception->errorCode);
            self::assertSame(2.0, $exception->retryAfterSeconds);
            self::assertIsArray($exception->details);
        }
    }

    public function testMissingHttpErrorCodeRemainsNull(): void
    {
        $transport = new QueueHttpClient([
            new Response(409, ['Content-Type' => 'application/json'], '{"error":{"message":"wait"}}'),
        ]);
        $client = new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport, maxRetries: 0));

        try {
            $client->request('get', '/api/v1/tasks/task_123');
            self::fail('Expected an API exception.');
        } catch (\RunApi\Core\Errors\RunApiException $exception) {
            self::assertNull($exception->errorCode);
        }
    }

    public function testContinuationErrorsPreserveCodesAndStatusClassification(): void
    {
        $cases = [
            [400, 'invalid_resource_id', \RunApi\Core\Errors\ValidationException::class],
            [409, 'request_conflict', RunApiException::class],
            [409, 'source_task_not_ready', RunApiException::class],
            [422, 'source_task_unusable', \RunApi\Core\Errors\ValidationException::class],
            [422, 'continuation_not_supported', \RunApi\Core\Errors\ValidationException::class],
            [429, 'rate_limited', RateLimitException::class],
            [503, 'continuation_unavailable', ServerException::class],
        ];

        foreach ($cases as [$status, $code, $exceptionClass]) {
            $transport = new QueueHttpClient([
                new Response($status, ['Content-Type' => 'application/json'], sprintf(
                    '{"error":{"code":"%s","message":"failed"}}',
                    $code,
                )),
            ]);
            $client = new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport, maxRetries: 0));

            try {
                $client->request('post', '/api/v1/continuation');
                self::fail('Expected an API exception.');
            } catch (RunApiException $exception) {
                self::assertInstanceOf($exceptionClass, $exception);
                self::assertSame($status, $exception->statusCode);
                self::assertSame($code, $exception->errorCode);
            }
        }
    }

    public function testSdkLocalErrorsDeclareExplicitCodes(): void
    {
        self::assertSame('validation', (new \RunApi\Core\Errors\ValidationException('invalid'))->errorCode);
        self::assertSame('authentication', (new \RunApi\Core\Errors\AuthenticationException('unauthorized'))->errorCode);
        self::assertSame('network', (new NetworkException('offline'))->errorCode);
        self::assertSame('rate_limit', (new RateLimitException('slow down'))->errorCode);
        self::assertSame('task_failed', (new \RunApi\Core\Errors\TaskFailedException('failed'))->errorCode);
        self::assertSame('task_timeout', (new \RunApi\Core\Errors\TaskTimeoutException('timeout'))->errorCode);
        self::assertSame('timeout', (new \RunApi\Core\Errors\TimeoutException('timeout'))->errorCode);
    }

    public function testRetriesIdempotentRequestsOnRetryableStatus(): void
    {
        $transport = new QueueHttpClient([
            new Response(503, ['Content-Type' => 'application/json'], '{"message":"busy"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
        ]);
        $client = new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            httpClient: $transport,
            maxRetries: 1,
            retryBaseDelaySeconds: 0.0,
        ));

        self::assertSame(['ok' => true], $client->request('get', '/api/v1/tasks/task_123'));
        self::assertCount(2, $transport->requests);
    }

    public function testDoesNotRetryNonIdempotentRequests(): void
    {
        $transport = new QueueHttpClient([
            new Response(503, ['Content-Type' => 'application/json'], '{"message":"busy"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
        ]);
        $client = new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            httpClient: $transport,
            maxRetries: 1,
            retryBaseDelaySeconds: 0.0,
        ));

        $this->expectException(ServerException::class);

        try {
            $client->request('post', '/api/v1/tasks', ['body' => ['prompt' => 'hello']]);
        } finally {
            self::assertCount(1, $transport->requests);
        }
    }

    public function testWrapsPsrClientExceptionsAsNetworkExceptions(): void
    {
        $previous = new FakeClientException('socket closed');
        $transport = new QueueHttpClient([$previous]);
        $client = new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            httpClient: $transport,
            maxRetries: 0,
        ));

        try {
            $client->request('get', '/api/v1/tasks/task_123');
            self::fail('Expected a network exception.');
        } catch (NetworkException $exception) {
            self::assertSame('Network error', $exception->getMessage());
            self::assertSame($previous, $exception->getPrevious());
        }
    }

    public function testAppliesRequestTimeoutWhenUsingGuzzleTransport(): void
    {
        $capturedOptions = [];
        $transport = new GuzzleClient([
            'handler' => static function (RequestInterface $_request, array $options) use (&$capturedOptions) {
                $capturedOptions[] = $options;

                return Create::promiseFor(new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'));
            },
        ]);
        $client = new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport, maxRetries: 0));

        self::assertSame(['ok' => true], $client->request('get', '/api/v1/me', [
            'options' => new RequestOptions(timeoutSeconds: 9),
        ]));

        self::assertSame(9, $capturedOptions[0]['timeout']);
        self::assertFalse($capturedOptions[0]['allow_redirects']);
        self::assertFalse($capturedOptions[0]['http_errors']);
        self::assertFalse($capturedOptions[0]['decode_content']);
    }

    public function testMapsErrorResponsesWhenUsingGuzzleTimeoutPath(): void
    {
        $transport = new GuzzleClient([
            'handler' => static fn (RequestInterface $_request, array $_options) => Create::promiseFor(
                new Response(429, ['Retry-After' => '2'], '{"error":{"message":"slow down","code":"rate_limit"}}'),
            ),
        ]);
        $client = new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport, maxRetries: 0));

        $this->expectException(RateLimitException::class);
        $this->expectExceptionMessage('slow down');

        $client->request('get', '/api/v1/me', [
            'options' => new RequestOptions(timeoutSeconds: 9),
        ]);
    }

    private function privateProperty(HttpClient $client, string $name): ClientInterface|RequestFactoryInterface|StreamFactoryInterface
    {
        $property = new \ReflectionProperty($client, $name);
        $value = $property->getValue($client);

        self::assertTrue(
            $value instanceof ClientInterface
            || $value instanceof RequestFactoryInterface
            || $value instanceof StreamFactoryInterface,
        );

        return $value;
    }
}
