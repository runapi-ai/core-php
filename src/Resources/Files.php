<?php

declare(strict_types=1);

namespace RunApi\Core\Resources;

use Psr\Http\Message\StreamInterface;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Http\MultipartBody;
use RunApi\Core\Http\MultipartFile;
use RunApi\Core\Models\FileUploadResponse;
use RunApi\Core\RequestOptions;

/**
 * Universal resource for uploading local paths, streams, remote URLs, or base64 payloads.
 */
final readonly class Files
{
    private const ENDPOINT = '/api/v1/files';

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

        $body = array_key_exists('file', $params)
            ? $this->multipartBody($params)
            : $this->jsonBody($params);

        return FileUploadResponse::fromArray($this->http->request('POST', self::ENDPOINT, [
            'body' => $body,
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
     */
    private function multipartBody(array $params): MultipartBody
    {
        $file = $params['file'];
        $fileName = $this->optionalString($params, 'file_name');
        $contentType = $this->optionalString($params, 'content_type');

        if (is_string($file)) {
            $multipartFile = MultipartFile::fromPath($file, $fileName, $contentType);
        } elseif ($file instanceof StreamInterface) {
            if ($fileName === null) {
                throw new ValidationException('file_name is required when file is a PSR-7 stream');
            }
            $multipartFile = MultipartFile::fromStream($file, $fileName, $contentType);
        } else {
            throw new ValidationException('file must be a local path or PSR-7 stream');
        }

        $fields = [];
        if ($fileName !== null) {
            $fields['file_name'] = $fileName;
        }

        return new MultipartBody(
            fields: $fields,
            files: ['file' => $multipartFile],
        );
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
}
