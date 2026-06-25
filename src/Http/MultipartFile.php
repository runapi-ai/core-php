<?php

declare(strict_types=1);

namespace RunApi\Core\Http;

use Psr\Http\Message\StreamInterface;
use RunApi\Core\Errors\ValidationException;

/**
 * File part used for multipart uploads.
 */
final readonly class MultipartFile
{
    private function __construct(
        public string $fileName,
        public ?string $path = null,
        public ?StreamInterface $stream = null,
        public ?string $contentType = null,
    ) {
        if (($path === null) === ($stream === null)) {
            throw new ValidationException('Exactly one multipart file source is required: path or stream');
        }
    }

    /**
     * Create a multipart file part from a local path.
     */
    public static function fromPath(string $path, ?string $fileName = null, ?string $contentType = null): self
    {
        $normalizedPath = self::nonBlank($path, 'file path');
        if (!is_file($normalizedPath) || !is_readable($normalizedPath)) {
            throw new ValidationException('file must be a readable local path');
        }

        return new self(
            fileName: self::nonBlank($fileName ?? basename($normalizedPath), 'file_name'),
            path: $normalizedPath,
            contentType: self::optionalNonBlank($contentType, 'content_type'),
        );
    }

    /**
     * Create a multipart file part from a PSR-7 stream.
     */
    public static function fromStream(StreamInterface $stream, string $fileName, ?string $contentType = null): self
    {
        return new self(
            fileName: self::nonBlank($fileName, 'file_name'),
            stream: $stream,
            contentType: self::optionalNonBlank($contentType, 'content_type'),
        );
    }

    private static function nonBlank(string $value, string $name): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new ValidationException($name . ' must not be blank');
        }

        return $trimmed;
    }

    private static function optionalNonBlank(?string $value, string $name): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::nonBlank($value, $name);
    }
}
