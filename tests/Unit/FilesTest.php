<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Resources\Files;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class FilesTest extends TestCase
{
    private const UPLOAD_JSON = '{"file_name":"image.png","url":"https://file.runapi.ai/temp/image.png","size_bytes":204800,"mime_type":"image/png","created_at":"2026-06-08T10:30:00Z","expires_at":"2026-06-08T11:30:00Z","custom":"kept"}';

    public function testCreateFromUrlSendsCanonicalJsonSource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $response = $files->create([
            'source' => ['type' => 'url', 'url' => 'https://cdn.runapi.ai/public/samples/mask.png'],
            'file_name' => 'image.png',
        ]);

        self::assertSame('/api/v1/files', $transport->requests[0]->getUri()->getPath());
        self::assertSame('application/json', $transport->requests[0]->getHeaderLine('Content-Type'));
        self::assertSame(
            '{"source":{"type":"url","url":"https:\/\/cdn.runapi.ai\/public\/samples\/mask.png"},"file_name":"image.png"}',
            (string) $transport->requests[0]->getBody(),
        );
        self::assertSame('image.png', $response->fileName);
        self::assertSame('https://file.runapi.ai/temp/image.png', $response->url);
        self::assertSame(204800, $response->sizeBytes);
        self::assertSame('image/png', $response->mimeType);
        self::assertSame('2026-06-08T10:30:00Z', $response->createdAt);
        self::assertSame('2026-06-08T11:30:00Z', $response->expiresAt);
        self::assertSame('kept', $response->toArray()['custom']);
    }

    public function testCreateFromBase64SendsCanonicalJsonSource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $files->create(['source' => ['type' => 'base64', 'data' => 'aGVsbG8=']]);

        self::assertSame(
            '{"source":{"type":"base64","data":"aGVsbG8="}}',
            (string) $transport->requests[0]->getBody(),
        );
    }

    public function testCreateFromPathSendsMultipartBody(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'runapi-php-sdk-');
        self::assertIsString($path);
        file_put_contents($path, 'image-bytes');
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        try {
            $files->create([
                'file' => $path,
                'file_name' => 'custom.png',
                'content_type' => 'image/png',
            ]);
        } finally {
            @unlink($path);
        }

        $request = $transport->requests[0];
        $body = (string) $request->getBody();
        self::assertStringStartsWith('multipart/form-data; boundary=runapi-', $request->getHeaderLine('Content-Type'));
        self::assertStringContainsString('name="file_name"', $body);
        self::assertStringContainsString('custom.png', $body);
        self::assertStringContainsString('name="file"; filename="custom.png"', $body);
        self::assertStringContainsString('Content-Type: image/png', $body);
        self::assertStringContainsString('image-bytes', $body);
    }

    public function testCreateFromPathUsesStreamingMultipartBody(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'runapi-php-sdk-');
        self::assertIsString($path);
        file_put_contents($path, 'image-bytes');
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        try {
            $files->create([
                'file' => $path,
                'file_name' => 'custom.png',
            ]);

            self::assertInstanceOf(MultipartStream::class, $transport->requests[0]->getBody());
        } finally {
            @unlink($path);
        }
    }

    public function testCreateFromPsrStreamRequiresFileName(): void
    {
        $files = new Files(new HttpClient(new ClientOptions(
            apiKey: 'test-key',
            httpClient: new QueueHttpClient([new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON)]),
        )));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('file_name is required when file is a PSR-7 stream');

        $files->create(['file' => Utils::streamFor('image-bytes')]);
    }

    public function testCreateFromPsrStreamSendsMultipartBody(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $files->create([
            'file' => Utils::streamFor('image-bytes'),
            'file_name' => 'stream.png',
        ]);

        $body = (string) $transport->requests[0]->getBody();
        self::assertStringContainsString('name="file"; filename="stream.png"', $body);
        self::assertStringContainsString('image-bytes', $body);
    }

    public function testRejectsMissingOrMultipleSourcesBeforeRequest(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        foreach ([[], ['file' => 'x', 'source' => ['type' => 'url', 'url' => 'https://cdn.runapi.ai/public/samples/mask.png']]] as $params) {
            try {
                $files->create($params);
                self::fail('Expected validation exception.');
            } catch (ValidationException $exception) {
                self::assertSame('Exactly one source is required: file or source', $exception->getMessage());
            }
        }

        self::assertSame([], $transport->requests);
    }

    public function testRejectsUnreadableLocalPathBeforeRequest(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], self::UPLOAD_JSON),
        ]);
        $files = new Files(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('file must be a readable local path');

        $files->create(['file' => '/path/that/does/not/exist.png']);
    }
}
