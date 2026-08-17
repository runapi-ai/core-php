<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Resources\Uploads;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

final class UploadsTest extends TestCase
{
    public function testLifecycleUsesCanonicalPathsAndBodies(): void
    {
        $upload = '{"id":"upload_123","object":"upload","bytes":3,"created_at":1,"filename":"data.bin","purpose":"user_data","status":"pending","expires_at":2}';
        $part = '{"id":"part_123","object":"upload.part","created_at":1,"upload_id":"upload_123"}';
        $transport = new QueueHttpClient([
            new Response(200, ['Content-Type' => 'application/json'], $upload),
            new Response(200, ['Content-Type' => 'application/json'], $part),
            new Response(200, ['Content-Type' => 'application/json'], $upload),
            new Response(200, ['Content-Type' => 'application/json'], $upload),
        ]);
        $uploads = new Uploads(new HttpClient(new ClientOptions(apiKey: 'test-key', httpClient: $transport)));
        $path = tempnam(sys_get_temp_dir(), 'runapi-part-');
        file_put_contents($path, 'abc');

        $uploads->create(['bytes' => 3, 'filename' => 'data.bin', 'mime_type' => 'application/octet-stream']);
        $uploads->addPart('upload_123', $path);
        $uploads->complete('upload_123', ['part_123']);
        $uploads->cancel('upload_123');

        self::assertSame('/v1/uploads', $transport->requests[0]->getUri()->getPath());
        self::assertSame('/v1/uploads/upload_123/parts', $transport->requests[1]->getUri()->getPath());
        self::assertSame('{"part_ids":["part_123"]}', (string) $transport->requests[2]->getBody());
        self::assertSame('/v1/uploads/upload_123/cancel', $transport->requests[3]->getUri()->getPath());
        unlink($path);
    }
}
