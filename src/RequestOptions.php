<?php

declare(strict_types=1);

namespace RunApi\Core;

/**
 * Per-request overrides that take precedence over client-level defaults.
 */
final readonly class RequestOptions
{
    /**
     * Create per-request overrides.
     */
    public function __construct(
        /** @var array<string, string> */
        public array $headers = [],
        public ?int $timeoutSeconds = null,
        public ?int $maxRetries = null,
        public ?float $pollIntervalSeconds = null,
        public ?float $maxWaitSeconds = null,
    ) {
    }
}
