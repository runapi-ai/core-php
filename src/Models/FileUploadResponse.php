<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * Response returned after uploading a file to RunAPI.
 */
readonly class FileUploadResponse extends BaseModel
{
    /**
     * Create a file upload response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public string $fileName,
        public string $url,
        public int $sizeBytes,
        public string $mimeType,
        public string $createdAt,
        public string $expiresAt,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'file_name' => $fileName,
            'url' => $url,
            'size_bytes' => $sizeBytes,
            'mime_type' => $mimeType,
            'created_at' => $createdAt,
            'expires_at' => $expiresAt,
        ] : $raw);
    }

    /**
     * Hydrate a file upload response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            fileName: Payload::string($raw, 'file_name'),
            url: Payload::string($raw, 'url'),
            sizeBytes: Payload::int($raw, 'size_bytes'),
            mimeType: Payload::string($raw, 'mime_type'),
            createdAt: Payload::string($raw, 'created_at'),
            expiresAt: Payload::string($raw, 'expires_at'),
            raw: $raw,
        );
    }
}
