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

    public function testUnknownActionAndModelAreNoOps(): void
    {
        $validator = new ContractValidator($this->repository());

        $validator->validate('unknown/action', 'kling-v2', []);
        $validator->validate('kling/text-to-video', 'unknown-model', []);

        self::assertNull($this->repository()->action('unknown/action'));
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
                        'duration' => ['min' => 5, 'max' => 10],
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
        ]);
    }
}
