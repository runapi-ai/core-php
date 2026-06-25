<?php

declare(strict_types=1);

namespace RunApi\Core\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RunApi\Core\Auth\ApiKeyResolver;
use RunApi\Core\ClientOptions;
use RunApi\Core\Constants;
use RunApi\Core\Errors\ErrorMapper;
use RunApi\Core\Errors\NetworkException;
use RunApi\Core\Errors\RateLimitException;
use RunApi\Core\Errors\RunApiException;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\RequestOptions;
use RunApi\Core\Support\Json;

/**
 * HTTP transport wrapper shared by RunAPI PHP clients.
 */
final class HttpClient
{
    private readonly ClientInterface $client;
    private readonly RequestFactoryInterface $requestFactory;
    private readonly StreamFactoryInterface $streamFactory;
    private readonly string $apiKey;
    private readonly string $baseUrl;

    /**
     * Create an HTTP transport with optional API key, base URL, and client overrides.
     */
    public function __construct(private readonly ClientOptions $options = new ClientOptions())
    {
        $this->apiKey = ApiKeyResolver::resolve($options->apiKey);
        $this->baseUrl = $options->resolvedBaseUrl();
        $this->client = $options->httpClient ?? $this->defaultClient($options);
        $factory = new HttpFactory();
        $this->requestFactory = $options->requestFactory ?? $factory;
        $this->streamFactory = $options->streamFactory ?? $factory;
    }

    /**
     * Send a RunAPI HTTP request and return the decoded response object.
     *
     * @param array<string, mixed> $request
     *
     * @return array<string, mixed>
     */
    public function request(string $method, string $path, array $request = []): array
    {
        $requestOptions = $request['options'] ?? null;
        if ($requestOptions !== null && !$requestOptions instanceof RequestOptions) {
            throw new \InvalidArgumentException('options must be an instance of RequestOptions');
        }

        $query = $request['query'] ?? [];
        if (!is_array($query)) {
            throw new \InvalidArgumentException('query must be an array');
        }

        $headers = $request['headers'] ?? [];
        if (!is_array($headers)) {
            throw new \InvalidArgumentException('headers must be an array');
        }
        if ($requestOptions !== null) {
            $headers = array_merge($requestOptions->headers, $headers);
        }

        $body = $request['body'] ?? null;
        $psrRequest = $this->buildRequest($method, $path, $query, $headers, $body);
        $response = $this->sendWithRetry($psrRequest, $requestOptions);
        $responseBody = (string) $response->getBody();

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw ErrorMapper::fromResponse($response, $responseBody);
        }

        return Json::decodeObject($responseBody);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $headers
     */
    private function buildRequest(string $method, string $path, array $query, array $headers, mixed $body): RequestInterface
    {
        $url = $this->buildUrl($path, $query);
        $request = $this->requestFactory->createRequest(strtoupper($method), $url);

        foreach ($this->options->headers as $name => $value) {
            $request = $request->withHeader((string) $name, (string) $value);
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader((string) $name, (string) $value);
        }

        $request = $request
            ->withHeader('Authorization', 'Bearer ' . $this->apiKey)
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', $this->options->userAgent);

        if (!$request->hasHeader('Accept-Encoding')) {
            $request = $request->withHeader('Accept-Encoding', 'identity');
        }

        if ($body instanceof MultipartBody) {
            $request = $this->withMultipartBody($request, $body);
        } elseif ($body !== null) {
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream(Json::encode($body)));
        }

        return $request;
    }

    private function sendWithRetry(RequestInterface $request, ?RequestOptions $requestOptions): ResponseInterface
    {
        $maxRetries = $requestOptions === null ? $this->options->maxRetries : ($requestOptions->maxRetries ?? $this->options->maxRetries);
        $attempt = 0;

        while (true) {
            try {
                $response = $this->sendRequest($request, $requestOptions);

                if (!$this->shouldRetryStatus($request, $response, $attempt, $maxRetries)) {
                    return $response;
                }

                $this->sleep($this->retryDelaySeconds($response, $attempt));
            } catch (ClientExceptionInterface $exception) {
                if (!$this->shouldRetryException($request, $attempt, $maxRetries)) {
                    throw new NetworkException('Network error', previous: $exception);
                }

                $this->sleep($this->backoffSeconds($attempt));
            }

            $attempt++;
        }
    }

    private function sendRequest(RequestInterface $request, ?RequestOptions $requestOptions): ResponseInterface
    {
        if (
            $this->client instanceof GuzzleClient
            && $requestOptions?->timeoutSeconds !== null
        ) {
            return $this->client->send($request, [
                'timeout' => $requestOptions->timeoutSeconds,
                'allow_redirects' => false,
                'http_errors' => false,
                'decode_content' => false,
            ]);
        }

        return $this->client->sendRequest($request);
    }

    private function shouldRetryStatus(RequestInterface $request, ResponseInterface $response, int $attempt, int $maxRetries): bool
    {
        if ($attempt >= $maxRetries || !$this->isIdempotent($request)) {
            return false;
        }

        return in_array($response->getStatusCode(), Constants::RETRYABLE_STATUS_CODES, true);
    }

    private function shouldRetryException(RequestInterface $request, int $attempt, int $maxRetries): bool
    {
        return $attempt < $maxRetries && $this->isIdempotent($request);
    }

    private function isIdempotent(RequestInterface $request): bool
    {
        return in_array(strtoupper($request->getMethod()), Constants::IDEMPOTENT_METHODS, true);
    }

    private function retryDelaySeconds(ResponseInterface $response, int $attempt): float
    {
        $retryAfter = $response->getHeaderLine('Retry-After');
        if ($retryAfter !== '') {
            $exception = new RateLimitException('Rate limited', is_numeric($retryAfter) ? (float) $retryAfter : null);
            if ($exception->retryAfterSeconds !== null) {
                return $exception->retryAfterSeconds;
            }
        }

        return $this->backoffSeconds($attempt);
    }

    private function backoffSeconds(int $attempt): float
    {
        $exponential = $this->options->retryBaseDelaySeconds * (2 ** $attempt);

        return min($this->options->retryMaxDelaySeconds, $exponential);
    }

    private function sleep(float $seconds): void
    {
        if ($seconds <= 0.0) {
            return;
        }

        usleep((int) round($seconds * 1_000_000));
    }

    private function withMultipartBody(RequestInterface $request, MultipartBody $body): RequestInterface
    {
        $stream = new MultipartStream($this->multipartElements($body), 'runapi-' . bin2hex(random_bytes(16)));

        return $request
            ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $stream->getBoundary())
            ->withBody($stream);
    }

    /**
     * @return list<array{name: string, contents: mixed, filename?: string, headers?: array<string, string>}>
     */
    private function multipartElements(MultipartBody $body): array
    {
        $elements = [];

        foreach ($body->fields as $name => $value) {
            $elements[] = [
                'name' => $name,
                'contents' => $value,
            ];
        }

        foreach ($body->files as $name => $file) {
            $element = [
                'name' => $name,
                'contents' => $this->multipartFileStream($file),
                'filename' => $file->fileName,
            ];
            if ($file->contentType !== null) {
                $element['headers'] = ['Content-Type' => $file->contentType];
            }
            $elements[] = $element;
        }

        return $elements;
    }

    private function multipartFileStream(MultipartFile $file): mixed
    {
        if ($file->path !== null) {
            try {
                return Utils::tryFopen($file->path, 'rb');
            } catch (\RuntimeException) {
                throw new ValidationException('file must be readable');
            }
        }

        if ($file->stream === null) {
            throw new ValidationException('multipart file stream is required');
        }

        return $file->stream;
    }

    /**
     * @param array<string, mixed> $query
     */
    private function buildUrl(string $path, array $query): string
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $query = array_filter(
            $query,
            static fn (mixed $value): bool => $value !== null,
        );

        if ($query === []) {
            return $url;
        }

        return $url . '?' . http_build_query($query);
    }

    private function defaultClient(ClientOptions $options): ClientInterface
    {
        if (!class_exists(GuzzleClient::class)) {
            throw new RunApiException('No default PSR-18 HTTP client is available. Install guzzlehttp/guzzle or pass httpClient.');
        }

        return new GuzzleClient([
            'timeout' => $options->timeoutSeconds,
        ]);
    }
}
