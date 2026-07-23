# RunAPI Core PHP SDK

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/core)](https://packagist.org/packages/runapi-ai/core)
[![License](https://img.shields.io/github/license/runapi-ai/core-php)](https://github.com/runapi-ai/core-php/blob/main/LICENSE)

The RunAPI Core PHP SDK provides shared authentication, PSR HTTP transport, retry, polling, typed errors, response objects, file upload, and account primitives for RunAPI PHP packages. Application code should normally install a concrete model package such as `runapi-ai/wan`; install `runapi-ai/core` directly only when building shared PHP SDK tooling.

## Install

```bash
composer require runapi-ai/core
```

## Requirements

- PHP 8.2 or newer
- Composer
- A PSR-18 / PSR-17 / PSR-7 compatible HTTP stack

The package includes a Guzzle-based default HTTP stack and also accepts custom PSR implementations.

## Request Identifiers And Safe Task Creation

Task-creation calls accept an optional opaque `Idempotency-Key` up to 512 characters. Generate one value per logical task and reuse it only with identical input after an unknown result. Reusing the value with different input returns `409 Conflict`; do not derive it from `X-Client-Request-Id`.

```php
use RunApi\Core\RequestOptions;

$options = new RequestOptions(headers: [
    'X-Client-Request-Id' => 'order-123',
    'Idempotency-Key' => 'opaque-logical-task-123',
]);
```

## Links

- SDK docs: https://runapi.ai/docs#runapi-sdks
- Model catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/core-php
- Multi-language SDK repository: https://github.com/runapi-ai/core-sdk

## License

Licensed under the Apache License, Version 2.0.
