<?php

declare(strict_types=1);

namespace RunApi\Core\Errors;

use Psr\Http\Message\ResponseInterface;

/**
 * Maps HTTP error responses to typed RunAPI exceptions.
 */
final class ErrorMapper
{
    /**
     * Narrow a polled task response after completion has been confirmed.
     */
    public static function fromResponse(ResponseInterface $response, string $body): RunApiException
    {
        $statusCode = $response->getStatusCode();
        $requestId = self::header($response, 'X-Request-ID');
        $retryAfterSeconds = self::retryAfterSeconds(self::header($response, 'Retry-After'));
        $details = self::decodeJson($body);
        $message = self::message($details) ?? sprintf('RunAPI request failed with status %d', $statusCode);
        $errorCode = self::errorCode($details);

        return match (true) {
            $statusCode === 401 => new AuthenticationException($message, $statusCode, $requestId, $errorCode, $details, $body),
            $statusCode === 402 => new InsufficientCreditsException($message, $statusCode, $requestId, $errorCode, $details, $body),
            $statusCode === 403 => new PermissionException($message, $statusCode, $requestId, $errorCode, $details, $body),
            $statusCode === 404 => new NotFoundException($message, $statusCode, $requestId, $errorCode, $details, $body),
            $statusCode === 429 => new RateLimitException(
                $message,
                $retryAfterSeconds,
                $statusCode,
                $requestId,
                $errorCode,
                $details,
                $body,
            ),
            $statusCode === 400 || $statusCode === 422 => new ValidationException($message, $statusCode, $requestId, $errorCode, $details, $body),
            $statusCode >= 500 => new ServerException($message, $statusCode, $requestId, $errorCode, $details, $body),
            default => new RunApiException($message, $statusCode, $requestId, $errorCode, $details, $body),
        };
    }

    private static function header(ResponseInterface $response, string $name): ?string
    {
        $value = $response->getHeaderLine($name);

        return $value === '' ? null : $value;
    }

    private static function retryAfterSeconds(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return max(0.0, (float) ($timestamp - time()));
    }

    private static function decodeJson(string $body): mixed
    {
        if (trim($body) === '') {
            return null;
        }

        $decoded = json_decode($body, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    private static function message(mixed $details): ?string
    {
        if (!is_array($details)) {
            return null;
        }

        $error = $details['error'] ?? null;
        if (is_string($error)) {
            return $error;
        }

        if (is_array($error) && isset($error['message']) && is_string($error['message'])) {
            return $error['message'];
        }

        $message = $details['message'] ?? null;

        return is_string($message) ? $message : null;
    }

    private static function errorCode(mixed $details): ?string
    {
        if (!is_array($details)) {
            return null;
        }

        $error = $details['error'] ?? null;
        if (is_array($error) && isset($error['code']) && is_string($error['code'])) {
            return $error['code'];
        }

        $code = $details['code'] ?? null;

        return is_string($code) ? $code : null;
    }
}
