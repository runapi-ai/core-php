<?php

declare(strict_types=1);

namespace RunApi\Core\Http;

/**
 * Multipart request body with form fields and streaming file parts.
 */
final readonly class MultipartBody
{
    /**
     * Create a multipart request body.
     *
     * @param array<string, string> $fields
     * @param array<string, MultipartFile> $files
     */
    public function __construct(
        public array $fields = [],
        public array $files = [],
    ) {
    }
}
