<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use Psr\Http\Message\StreamInterface;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Http\MultipartBody;
use RunApi\Core\Http\MultipartFile;
use RunApi\Core\Models\DeletedProtocolFile;
use RunApi\Core\Models\FileUploadResponse;
use RunApi\Core\Models\ProtocolFile;
use RunApi\Core\Models\ProtocolFileList;
use RunApi\Core\RequestOptions;

/**
 * Universal resource for uploading local paths, streams, remote URLs, or base64 payloads.
 */
final readonly class Files
{
    private const ENDPOINT = '/api/v1/files';
    private const PREPARE_ENDPOINT = self::ENDPOINT . '/prepare';
    private const CONFIRM_ENDPOINT = self::ENDPOINT . '/confirm';
    private const PROTOCOL_ENDPOINT = '/v1/files';

    /**
     * Create a resource using the shared RunAPI HTTP transport.
     */
    public function __construct(private HttpClient $http)
    {
    }

    /**
     * Create a files task and return immediately with a task id.
     *
     * @param array<string, mixed> $params
     */
    public function create(array $params, ?RequestOptions $options = null): FileUploadResponse
    {
        $this->validateSourceCount($params);

        if (array_key_exists('file', $params)) {
            return $this->uploadDirect($params, $options);
        }

        return FileUploadResponse::fromArray($this->http->request('POST', self::ENDPOINT, [
            'body' => $this->jsonBody($params),
            'options' => $options,
        ]));
    }

    /**
     * @param array<string, mixed> $params Must include file as a local path or named PSR-7 stream.
     */
    public function createFile(array $params, ?RequestOptions $options = null): ProtocolFile
    {
        $fileName = $this->optionalString($params, 'file_name');
        $contentType = $this->optionalString($params, 'content_type');
        $file = $params['file'] ?? null;
        if (is_string($file)) {
            $part = MultipartFile::fromPath($file, $fileName, $contentType);
        } elseif ($file instanceof StreamInterface && $fileName !== null) {
            $part = MultipartFile::fromStream($file, $fileName, $contentType);
        } else {
            throw new ValidationException('file must be a local path or a named PSR-7 stream');
        }
        $raw = $this->http->request('POST', self::PROTOCOL_ENDPOINT, [
            'body' => new MultipartBody(
                fields: ['purpose' => $this->optionalString($params, 'purpose') ?? 'user_data'],
                files: ['file' => $part],
            ),
            'options' => $options,
        ]);
        return ProtocolFile::fromArray($raw);
    }

    public function list(?string $after = null, ?int $limit = null, ?string $order = null, ?string $purpose = null, ?RequestOptions $options = null): ProtocolFileList
    {
        $query = array_filter(compact('after', 'limit', 'order', 'purpose'), static fn (mixed $value): bool => $value !== null);
        return ProtocolFileList::fromArray($this->http->request('GET', self::PROTOCOL_ENDPOINT, [
            'query' => $query,
            'options' => $options,
        ]));
    }

    public function retrieve(string $fileId, ?RequestOptions $options = null): ProtocolFile
    {
        return ProtocolFile::fromArray($this->http->request('GET', $this->protocolFilePath($fileId), ['options' => $options]));
    }

    public function content(string $fileId, ?RequestOptions $options = null): string
    {
        return $this->http->requestRaw('GET', $this->protocolFilePath($fileId) . '/content', ['options' => $options])->body;
    }

    public function deleteFile(string $fileId, ?RequestOptions $options = null): DeletedProtocolFile
    {
        return DeletedProtocolFile::fromArray($this->http->request('DELETE', $this->protocolFilePath($fileId), ['options' => $options]));
    }

    /**
     * Local files upload straight to storage: ask for a pre-authorized target,
     * PUT the bytes there (never through the API), then confirm. The caller still
     * makes a single create call.
     *
     * @param array<string, mixed> $params
     */
    private function uploadDirect(array $params, ?RequestOptions $options): FileUploadResponse
    {
        $file = $params['file'];
        $fileName = $this->optionalString($params, 'file_name');

        if (is_string($file)) {
            if (!is_file($file) || !is_readable($file)) {
                throw new ValidationException('file must be a readable local path');
            }
            $data = (string) file_get_contents($file);
            $resolvedName = $fileName ?? basename($file);
        } elseif ($file instanceof StreamInterface) {
            if ($fileName === null) {
                throw new ValidationException('file_name is required when file is a PSR-7 stream');
            }
            if ($file->isSeekable()) {
                $file->rewind();
            }
            $data = $file->getContents();
            $resolvedName = $fileName;
        } else {
            throw new ValidationException('file must be a local path or PSR-7 stream');
        }

        $prepared = $this->http->request('POST', self::PREPARE_ENDPOINT, [
            'body' => [
                'filename' => $resolvedName,
                'byte_size' => strlen($data),
                'checksum' => base64_encode(md5($data, true)),
            ],
            'options' => $options,
        ]);

        $this->http->upload($prepared['upload_url'], $prepared['headers'], $data);

        return FileUploadResponse::fromArray($this->http->request('POST', self::CONFIRM_ENDPOINT, [
            'body' => ['signed_id' => $prepared['signed_id']],
            'options' => $options,
        ]));
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateSourceCount(array $params): void
    {
        $sourceCount = 0;
        if (array_key_exists('file', $params) && $this->present($params['file'])) {
            $sourceCount++;
        }
        if (array_key_exists('source', $params) && $this->present($params['source'])) {
            $sourceCount++;
        }

        if ($sourceCount !== 1) {
            throw new ValidationException('Exactly one source is required: file or source');
        }
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function jsonBody(array $params): array
    {
        $source = $params['source'];
        if (!is_array($source)) {
            throw new ValidationException('source must be an array');
        }

        /** @var array<string, mixed> $source */
        $type = $this->requiredString($source, 'type');
        if ($type !== 'url' && $type !== 'base64') {
            throw new ValidationException('source.type must be url or base64');
        }

        $bodySource = ['type' => $type];
        if ($type === 'url') {
            $bodySource['url'] = $this->requiredString($source, 'url');
        } else {
            $bodySource['data'] = $this->requiredString($source, 'data');
        }

        $body = ['source' => $bodySource];
        $fileName = $this->optionalString($params, 'file_name');
        if ($fileName !== null) {
            $body['file_name'] = $fileName;
        }

        return $body;
    }

    private function present(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        return !is_string($value) || trim($value) !== '';
    }

    /**
     * @param array<string, mixed> $params
     */
    private function requiredString(array $params, string $key): string
    {
        $value = $params[$key] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new ValidationException($key . ' must be a non-blank string');
        }

        return trim($value);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function optionalString(array $params, string $key): ?string
    {
        if (!array_key_exists($key, $params) || $params[$key] === null) {
            return null;
        }

        return $this->requiredString($params, $key);
    }

    private function protocolFilePath(string $fileId): string
    {
        return self::PROTOCOL_ENDPOINT . '/' . rawurlencode($this->requiredString(['file_id' => $fileId], 'file_id'));
    }
}
