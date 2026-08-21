# Changelog

## [v0.6.0](https://github.com/runapi-ai/core-php/releases/tag/v0.6.0) - 2026-08-21

### Added
- Add generated validation metadata for Grok Imagine Image 2.0 task requests.
- Accept 1080p output resolution for Seedance 2.5 video generation.
- Add shared contract validation for Kling V3 Omni reference-image and source-video editing workflows.


## [v0.5.0](https://github.com/runapi-ai/core-php/releases/tag/v0.5.0) - 2026-08-17

### Added
- Publish reusable voice create, list, get, and text-to-speech input contract metadata.
- Create, list, retrieve, download, and delete persistent Files, and compose Files from multipart Uploads while preserving temporary file uploads.

### Fixed
- Reject Suno text-to-music auto_lyrics prompts longer than 3000 characters before creating a task.


## [v0.4.2](https://github.com/runapi-ai/core-php/releases/tag/v0.4.2) - 2026-08-14

### Fixed
- Accept enable_safety_checker for Hailuo 02 Pro image-to-video and Wan 2.7 video edit requests.


## [v0.4.1](https://github.com/runapi-ai/core-php/releases/tag/v0.4.1) - 2026-08-12

### Added
- Export the Seedream layer decomposition request contract for PHP SDK validation.
- Add shared contract constraints for Seedance 2.5 requests.

### Fixed
- Apply the shared Runway aspect_ratio and first_frame_image_url conditional rules in PHP request validation.
- Reject enable_safety_checker for Hailuo 02 Pro image-to-video and Wan 2.7 video edit requests.


## [v0.4.0](https://github.com/runapi-ai/core-php/releases/tag/v0.4.0) - 2026-08-10

### Breaking
- Reject OmniHuman audio-to-video prompts over 300 characters before sending the request.
  Migration: Upgrade the package and shorten OmniHuman audio-to-video prompts to 300 characters or fewer.

### Added
- Add shared contract constraints for Suno music inspiration requests.

### Changed
- Update generated Seedream 5 Lite output quality constraints.


## [v0.3.0](https://github.com/runapi-ai/core-php/releases/tag/v0.3.0) - 2026-08-07

### Added
- Validate the optional multi_shots field for supported WAN 2.6 video requests.
- Preserve repeated multipart fields and successful JSON, text, and subtitle response bodies.
- Add generated MiniMax H3 input contract metadata.
- Add shared PHP request validation metadata for Qwen 3 image endpoints.
- Add Fish Audio s2.1-pro and MP3 or WAV output constraints to shared PHP contract metadata.

### Changed
- Update generated Grok Imagine Preview resolution and reference image constraints.

### Fixed
- Validate the 10 to 360 second custom duration range and reject controls that the selected vocal mode or model cannot honor.


## [v0.2.10](https://github.com/runapi-ai/core-php/releases/tag/v0.2.10) - 2026-08-06

### Added
- Register the stitching, remastering, and sampling actions in shared contract metadata.

### Fixed
- Validate the optional PixVerse enable_audio field for text-to-video and image-to-video requests.


## [v0.2.9](https://github.com/runapi-ai/core-php/releases/tag/v0.2.9) - 2026-08-04

### Added
- Register the five PixVerse V6 video actions in shared contract metadata.


## [v0.2.8](https://github.com/runapi-ai/core-php/releases/tag/v0.2.8) - 2026-07-31

### Added
- Expose generic cache-write prices separately from TTL-specific cache-write prices.

### Removed
- Remove seedance-v1-lite from shared Seedance contract metadata.
  Migration: Use seedance-v1-pro or another supported Seedance model.


## [v0.2.7](https://github.com/runapi-ai/core-php/releases/tag/v0.2.7) - 2026-07-29

### Removed
- Remove seedance-v1-lite from shared Seedance contract metadata.
  Migration: Use seedance-v1-pro or another supported Seedance model.


## [v0.2.6](https://github.com/runapi-ai/core-php/releases/tag/v0.2.6) - 2026-07-28

### Changed
- Describe and validate the documented Gemini Omni, Grok Imagine, and Topaz request fields.

### Fixed
- Validate required audio and music request fields before sending requests.
- Carry supported Wan Flash image-to-video duration values in generated contract metadata; request defaults remain API-applied.


## [v0.2.5](https://github.com/runapi-ai/core-php/releases/tag/v0.2.5) - 2026-07-28

### Added
- Expose typed live Price Schedule, Price Quote, and Task Billing Facts through every Provider Client.
- Add generated contract metadata required by Kling O1 reference-media validation.
- Expose whether an async create response reused an idempotent task.

### Changed
- Support generated Flux 2 Max request validation metadata used by the Flux 2 PHP SDK.


## [v0.2.4](https://github.com/runapi-ai/core-php/releases/tag/v0.2.4) - 2026-07-23

### Added
- Add generated contract metadata required by Kling 2.6 motion-control validation.


## [v0.2.3](https://github.com/runapi-ai/core-php/releases/tag/v0.2.3) - 2026-07-23

### Added
- Add Kling V3 Omni text-to-video and image-to-video constraints to shared PHP contract metadata.
- Add generated validation metadata for seven additional Producer FUZZ music generation versions.
- Add generated contract metadata required by the Kling video continuation resource.


## [v0.2.2](https://github.com/runapi-ai/core-php/releases/tag/v0.2.2) - 2026-07-22

### Added
- Add Kling 2.6 text-to-video and image-to-video constraints to shared PHP contract metadata.
- Add generated validation metadata for Midjourney first-video extension requests.
- Add generated validation metadata for Flux text-to-image and remix-image requests.
- Publish Veo 3.1 Lite model and input constraints in aggregate Composer contract metadata.
- Add shared PHP request validation metadata for Qwen Image endpoints.


## [v0.2.1](https://github.com/runapi-ai/core-php/releases/tag/v0.2.1) - 2026-07-21

### Added
- Add generated validation metadata for lyrics generation and lyric blending requests.

### Changed
- Publish Seedance 1.5 Pro and V1 Pro Fast seed constraints in aggregate contract metadata for Composer packages.


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
