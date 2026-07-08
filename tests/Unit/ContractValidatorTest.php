<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RunApi\Core\Contract\ContractRepository;
use RunApi\Core\Contract\ContractValidator;
use RunApi\Core\Errors\ValidationException;

final class ContractValidatorTest extends TestCase
{
    public function testAcceptsValidParams(): void
    {
        $validator = new ContractValidator($this->repository());

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);

        self::assertSame(['kling-v2'], $this->repository()->action('kling/text-to-video')['models'] ?? null);
    }

    public function testRejectsMissingRequiredField(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('prompt is required');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);
    }

    public function testRejectsMissingRequiredStringField(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('source_image_url is required');

        $validator->validate('grok-imagine/edit-image', 'grok-imagine-edit-image', [
            'prompt' => 'A product render',
        ]);
    }

    public function testRejectsMissingRequiredArrayField(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('source_image_urls is required');

        $validator->validate('nano-banana/edit-image', 'nano-banana-edit', [
            'prompt' => 'A product render',
        ]);
    }

    public function testRejectsEnumMismatch(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('aspect_ratio must be one of the allowed values');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '4:3',
            'duration' => 10,
        ]);
    }

    public function testRejectsValueBelowMinimum(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('duration must be greater than or equal to 5');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 4,
        ]);
    }

    public function testRejectsValueAboveMaximum(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('duration must be less than or equal to 10');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 11,
        ]);
    }

    public function testValidatesStringRangeAsLength(): void
    {
        $validator = new ContractValidator($this->repository());

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'abc',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('prompt must be at most 24 characters');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'A prompt that is far too long',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);
    }

    public function testRejectsForbiddenRuleField(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('output_resolution is not allowed when model is nano-banana-2-lite');

        $validator->validate('nano-banana/text-to-image', 'nano-banana-2-lite', [
            'prompt' => 'A product render',
            'aspect_ratio' => 'auto',
            'output_resolution' => '1k',
        ]);
    }

    public function testRejectsStringBelowMinimumLength(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('prompt must be at least 3 characters');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'prompt' => 'ok',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);
    }

    public function testUnknownActionIsNoOp(): void
    {
        $validator = new ContractValidator($this->repository());

        $validator->validate('unknown/action', 'kling-v2', []);

        self::assertNull($this->repository()->action('unknown/action'));
    }

    public function testRejectsUnknownModel(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('model must be one of the allowed values');

        $validator->validate('kling/text-to-video', 'unknown-model', [
            'model' => 'unknown-model',
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);
    }

    public function testRejectsMissingModel(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('model is required');

        $validator->validate('kling/text-to-video', '_', [
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 10,
        ]);
    }

    public function testRejectsNonIntegerField(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('duration must be an integer between 5 and 10');

        $validator->validate('kling/text-to-video', 'kling-v2', [
            'model' => 'kling-v2',
            'prompt' => 'A neon city at night',
            'aspect_ratio' => '16:9',
            'duration' => 5.5,
        ]);
    }

    public function testEnforcesDeclaredForbiddenRules(): void
    {
        $validator = new ContractValidator($this->repository());

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('first_frame_image_url is not allowed when model is seedance-2.0 and output_resolution is 4k');

        $validator->validate('seedance/text-to-video', 'seedance-2.0', [
            'model' => 'seedance-2.0',
            'prompt' => 'A cinematic city flyover',
            'output_resolution' => '4k',
            'first_frame_image_url' => 'https://file.runapi.ai/first.png',
        ]);
    }

    private function repository(): ContractRepository
    {
        return new ContractRepository([
            'kling/text-to-video' => [
                'models' => ['kling-v2'],
                'fields_by_model' => [
                    'kling-v2' => [
                        'prompt' => ['required' => true, 'min' => 3, 'max' => 24],
                        'aspect_ratio' => ['enum' => ['16:9', '9:16']],
                        'duration' => ['min' => 5, 'max' => 10, 'type' => 'integer'],
                    ],
                ],
            ],
            'grok-imagine/edit-image' => [
                'models' => ['grok-imagine-edit-image'],
                'fields_by_model' => [
                    'grok-imagine-edit-image' => [
                        'prompt' => ['required' => true],
                        'source_image_url' => ['required' => true],
                    ],
                ],
            ],
            'nano-banana/edit-image' => [
                'models' => ['nano-banana-edit'],
                'fields_by_model' => [
                    'nano-banana-edit' => [
                        'prompt' => ['required' => true],
                        'source_image_urls' => ['required' => true],
                    ],
                ],
            ],
            'nano-banana/text-to-image' => [
                'models' => ['nano-banana-2-lite'],
                'rules' => [
                    [
                        'when' => ['model' => 'nano-banana-2-lite'],
                        'forbidden' => ['output_resolution', 'output_format'],
                    ],
                ],
                'fields_by_model' => [
                    'nano-banana-2-lite' => [
                        'prompt' => ['required' => true],
                        'aspect_ratio' => ['required' => true, 'enum' => ['auto', '16:9']],
                        'output_resolution' => [],
                        'output_format' => [],
                    ],
                ],
            ],
            'seedance/text-to-video' => [
                'models' => ['seedance-2.0'],
                'fields_by_model' => [
                    'seedance-2.0' => [
                        'prompt' => ['required' => true],
                        'output_resolution' => ['enum' => ['480p', '720p', '1080p', '4k']],
                    ],
                ],
                'rules' => [
                    [
                        'when' => ['model' => 'seedance-2.0', 'output_resolution' => '4k'],
                        'forbidden' => ['first_frame_image_url'],
                    ],
                ],
            ],
        ]);
    }
}
