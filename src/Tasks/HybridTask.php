<?php

declare(strict_types=1);

namespace RunApi\Core\Tasks;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\RawResponse;
use RunApi\Core\Support\Json;

/** An accepted Task Result URL supplied by a hybrid endpoint. */
final readonly class HybridTask
{
    public function __construct(
        public string $id,
        public string $location,
        public ?float $retryAfterSeconds,
    ) {
    }

    public static function fromAcceptance(RawResponse $response): self
    {
        $payload = Json::decodeObject($response->body);
        $id = $payload['id'] ?? null;
        $location = $response->header('Location');
        if (!is_string($id) || $id === '' || $location === null || trim($location) === '') {
            throw new ValidationException('Accepted Task response must include id and Location');
        }

        return new self($id, $location, self::retryAfter($response));
    }

    public static function retryAfter(RawResponse $response): ?float
    {
        $value = $response->header('Retry-After');

        if ($value === null) {
            return null;
        }
        if (is_numeric(trim($value))) {
            return max(0.0, (float) $value);
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : max(0.0, $timestamp - time());
    }
}
