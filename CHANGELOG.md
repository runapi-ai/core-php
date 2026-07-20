# Changelog

## [v0.2.0](https://github.com/runapi-ai/core-php/releases/tag/v0.2.0) - 2026-07-20

### Breaking
- Replace Grok Imagine image-to-video `source_image_urls` contract metadata with scalar `source_image_url`.
  Migration: Validate and send the source image through `source_image_url`.

### Added
- Add shared PHP contract metadata for OpenAI TTS and Fish Audio clients.
- Add model-specific contract validation metadata for Gemini Omni Flash Preview text-to-video requests.
- Publish Gemini TTS model and input contract metadata for Composer packages.
- Publish Seedream 5 Pro model and input contract metadata for Composer packages.
- Publish shared PHP contract metadata for the Producer text-to-music request schema.

### Changed
- Publish PHP core contract metadata used by the Midjourney prompt shortening resource.
- Publish Seedream 5-Lite output format contract metadata for Composer packages.
- Publish advanced stem separation mode, stem values, and conditional validation metadata for Composer packages.
- Validate array types and generated minimum and maximum item counts before sending requests.

### Fixed
- Publish model-specific contract rules that reject `seed` for Wan 2.6 video requests.
- Preserve API-provided error codes, leave missing codes unset, and use SDK exception types for local failures.
- Recognize continuation request failures across HTTP 400, 409, 422, 429, and 503 responses.


## [v0.1.7](https://github.com/runapi-ai/core-php/releases/tag/v0.1.7) - 2026-07-17

### Changed
- Publish PHP core contract metadata used by the Midjourney Composer package.
- Publish PHP core contract metadata used by the Grok Imagine Composer package.

## [v0.1.6](https://github.com/runapi-ai/core-php/releases/tag/v0.1.6) - 2026-07-16

### Changed
- Add Kling V3 Turbo text-to-video and image-to-video contract metadata to the PHP core package.
- Include generated validation metadata for the new Kling V3 Turbo variants.
- Publish PHP core contract validation updates for rule ordering, field presence, and integer and length constraints.

## [v0.1.5](https://github.com/runapi-ai/core-php/releases/tag/v0.1.5) - 2026-07-08

### Changed
- Refresh PHP core contract validation for updated model constraints.

## [v0.1.4](https://github.com/runapi-ai/core-php/releases/tag/v0.1.4) - 2026-07-08

### Changed
- Refresh PHP core contract metadata and validation fixtures for the current public API catalog.

## [v0.1.3](https://github.com/runapi-ai/core-php/releases/tag/v0.1.3) - 2026-07-07

### Changed
- Refresh RunAPI contract validation support.
- Publish v0.1.3.

## [v0.1.1](https://github.com/runapi-ai/core-php/releases/tag/v0.1.1) - 2026-07-02

### Changed
- PHP core SDK: file-upload (Files) and HTTP transport updates.

## [v0.1.0](https://github.com/runapi-ai/core-php/releases/tag/v0.1.0) - 2026-06-25

### Added
- Publish the first RunAPI PHP Composer package release for `runapi-ai/core`.
- Include typed PHP client resources, package README, Apache-2.0 license, and Composer CI.
