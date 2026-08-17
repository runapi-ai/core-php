<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use Psr\Http\Message\StreamInterface;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Http\MultipartBody;
use RunApi\Core\Http\MultipartFile;
use RunApi\Core\Models\ProtocolUpload;
use RunApi\Core\Models\ProtocolUploadPart;
use RunApi\Core\RequestOptions;

final readonly class Uploads
{
    private const ENDPOINT = '/v1/uploads';

    public function __construct(private HttpClient $http)
    {
    }

    /** @param array{bytes: int, filename: string, mime_type: string, purpose?: string} $params */
    public function create(array $params, ?RequestOptions $options = null): ProtocolUpload
    {
        $params['purpose'] ??= 'user_data';
        return ProtocolUpload::fromArray($this->http->request('POST', self::ENDPOINT, [
            'body' => $params, 'options' => $options,
        ]));
    }

    /** @param mixed $data Local path or named PSR-7 stream. */
    public function addPart(string $uploadId, mixed $data, ?string $fileName = null, ?string $contentType = null, ?RequestOptions $options = null): ProtocolUploadPart
    {
        if (is_string($data)) {
            $part = MultipartFile::fromPath($data, $fileName, $contentType);
        } elseif ($data instanceof StreamInterface && $fileName !== null) {
            $part = MultipartFile::fromStream($data, $fileName, $contentType);
        } else {
            throw new ValidationException('data must be a local path or a named PSR-7 stream');
        }
        return ProtocolUploadPart::fromArray($this->http->request('POST', $this->uploadPath($uploadId) . '/parts', [
            'body' => new MultipartBody(files: ['data' => $part]), 'options' => $options,
        ]));
    }

    /** @param list<string> $partIds */
    public function complete(string $uploadId, array $partIds, ?RequestOptions $options = null): ProtocolUpload
    {
        return ProtocolUpload::fromArray($this->http->request('POST', $this->uploadPath($uploadId) . '/complete', [
            'body' => ['part_ids' => $partIds], 'options' => $options,
        ]));
    }

    public function cancel(string $uploadId, ?RequestOptions $options = null): ProtocolUpload
    {
        return ProtocolUpload::fromArray($this->http->request('POST', $this->uploadPath($uploadId) . '/cancel', [
            'body' => [], 'options' => $options,
        ]));
    }

    private function uploadPath(string $uploadId): string
    {
        $id = trim($uploadId);
        if ($id === '') {
            throw new ValidationException('upload_id must be a non-blank string');
        }
        return self::ENDPOINT . '/' . rawurlencode($id);
    }
}
