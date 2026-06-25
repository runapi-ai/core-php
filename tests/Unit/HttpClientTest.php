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
