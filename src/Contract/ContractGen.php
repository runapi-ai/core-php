<?php

declare(strict_types=1);

namespace RunApi\Core\Contract;

/**
 * Generated request validation contract used by PHP SDK resources.
 */
final class ContractGen
{
    private function __construct()
    {
    }

    /**
     * @return array<string, array{models: list<string>, fields_by_model: array<string, array<string, array<string, mixed>>>, rules?: list<array{when?: array<string, mixed>, required?: list<string>, forbidden?: list<string>}>}>
     */
    public static function contract(): array
    {
        static $contract = null;

        return $contract ??= [
            'elevenlabs/isolate-audio' => [
                'models' => ['audio-isolation'],
                'fields_by_model' => [
                    'audio-isolation' => [
                        'source_audio_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'elevenlabs/speech-to-text' => [
                'models' => ['speech-to-text'],
                'fields_by_model' => [
                    'speech-to-text' => [
                        'source_audio_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'elevenlabs/text-to-dialogue' => [
                'models' => ['text-to-dialogue-v3'],
                'fields_by_model' => [
                    'text-to-dialogue-v3' => [
                        'dialogue' => [
                            'required' => true,
                        ],
                        'stability' => [
                            'enum' => [0.0, 0.5, 1.0],
                        ],
                    ],
                ],
            ],
            'elevenlabs/text-to-sound' => [
                'models' => ['sound-effect-v2'],
                'fields_by_model' => [
                    'sound-effect-v2' => [
                        'output_format' => [
                            'enum' => ['mp3_22050_32', 'mp3_44100_32', 'mp3_44100_64', 'mp3_44100_96', 'mp3_44100_128', 'mp3_44100_192', 'pcm_8000', 'pcm_16000', 'pcm_22050', 'pcm_24000', 'pcm_44100', 'pcm_48000', 'ulaw_8000', 'alaw_8000', 'opus_48000_32', 'opus_48000_64', 'opus_48000_96', 'opus_48000_128', 'opus_48000_192'],
                        ],
                        'text' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'elevenlabs/text-to-speech' => [
                'models' => ['text-to-speech-multilingual-v2', 'text-to-speech-turbo-v2.5'],
                'fields_by_model' => [
                    'text-to-speech-multilingual-v2' => [
                        'text' => [
                            'required' => true,
                        ],
                    ],
                    'text-to-speech-turbo-v2.5' => [
                        'text' => [
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'text-to-speech-multilingual-v2',
                    ],
                    'required' => ['voice'],
                ]],
            ],
            'fish-audio/text-to-speech' => [
                'models' => ['s1', 's2-pro'],
                'fields_by_model' => [
                    's1' => [
                        'text' => [
                            'required' => true,
                        ],
                    ],
                    's2-pro' => [
                        'text' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'flux-2/remix-image' => [
                'models' => ['flux-2-flex-remix-image', 'flux-2-pro-remix-image'],
                'fields_by_model' => [
                    'flux-2-flex-remix-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '3:2', '2:3', 'auto'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 8,
                        ],
                    ],
                    'flux-2-pro-remix-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '3:2', '2:3', 'auto'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 8,
                        ],
                    ],
                ],
            ],
            'flux-2/text-to-image' => [
                'models' => ['flux-2-flex-text-to-image', 'flux-2-pro-text-to-image'],
                'fields_by_model' => [
                    'flux-2-flex-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '3:2', '2:3'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'flux-2-pro-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '3:2', '2:3'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'flux-kontext/text-to-image' => [
                'models' => ['flux-kontext-max', 'flux-kontext-pro'],
                'fields_by_model' => [
                    'flux-kontext-max' => [
                        'aspect_ratio' => [
                            'enum' => ['21:9', '16:9', '4:3', '1:1', '3:4', '9:16'],
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_format' => [
                            'enum' => ['jpeg', 'png'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'safety_tolerance' => [
                            'type' => 'integer',
                        ],
                    ],
                    'flux-kontext-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['21:9', '16:9', '4:3', '1:1', '3:4', '9:16'],
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_format' => [
                            'enum' => ['jpeg', 'png'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'safety_tolerance' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'gemini-omni/create-audio' => [
                'models' => ['gemini-omni-audio'],
                'fields_by_model' => [
                    'gemini-omni-audio' => [
                        'audio_id' => [
                            'required' => true,
                        ],
                        'name' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'gemini-omni/create-character' => [
                'models' => ['gemini-omni-character'],
                'fields_by_model' => [
                    'gemini-omni-character' => [
                        'descriptions' => [
                            'required' => true,
                        ],
                        'reference_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'gemini-omni/text-to-video' => [
                'models' => ['gemini-omni-flash-preview', 'gemini-omni-text-to-video'],
                'fields_by_model' => [
                    'gemini-omni-flash-preview' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'gemini-omni-text-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16'],
                        ],
                        'audio_ids' => [
                            'max_items' => 3,
                        ],
                        'character_ids' => [
                            'max_items' => 3,
                        ],
                        'duration_seconds' => [
                            'enum' => [4, 6, 8, 10],
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p', '4k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'reference_image_urls' => [
                            'max_items' => 7,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'video_list' => [
                            'max_items' => 1,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'gemini-omni-flash-preview',
                    ],
                    'forbidden' => ['reference_image_urls', 'audio_ids', 'video_list', 'character_ids', 'duration_seconds', 'seed'],
                ]],
            ],
            'gemini-tts/text-to-speech' => [
                'models' => ['gemini-2.5-pro-tts', 'gemini-3.1-flash-tts'],
                'fields_by_model' => [
                    'gemini-2.5-pro-tts' => [
                        'dialogue_turns' => [
                            'required' => true,
                            'min_items' => 1,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'speakers' => [
                            'required' => true,
                            'min_items' => 1,
                        ],
                        'temperature' => [
                            'min' => 0,
                            'max' => 2,
                        ],
                    ],
                    'gemini-3.1-flash-tts' => [
                        'dialogue_turns' => [
                            'required' => true,
                            'min_items' => 1,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'speakers' => [
                            'required' => true,
                            'min_items' => 1,
                        ],
                        'temperature' => [
                            'min' => 0,
                            'max' => 2,
                        ],
                    ],
                ],
            ],
            'gpt-4o-image/text-to-image' => [
                'models' => ['gpt-4o-image'],
                'fields_by_model' => [
                    'gpt-4o-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:2', '2:3'],
                            'required' => true,
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 4],
                            'type' => 'integer',
                        ],
                        'source_image_urls' => [
                            'max_items' => 5,
                        ],
                    ],
                ],
            ],
            'gpt-image-2/edit-image' => [
                'models' => ['gpt-image-2'],
                'fields_by_model' => [
                    'gpt-image-2' => [
                        'aspect_ratio' => [
                            'enum' => ['auto', '1:1', '3:2', '2:3', '4:3', '3:4', '5:4', '4:5', '16:9', '9:16', '2:1', '1:2', '3:1', '1:3', '21:9', '9:21'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'gpt-image-2/text-to-image' => [
                'models' => ['gpt-image-2'],
                'fields_by_model' => [
                    'gpt-image-2' => [
                        'aspect_ratio' => [
                            'enum' => ['auto', '1:1', '3:2', '2:3', '4:3', '3:4', '5:4', '4:5', '16:9', '9:16', '2:1', '1:2', '3:1', '1:3', '21:9', '9:21'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'gpt-image/edit-image' => [
                'models' => ['gpt-image-1.5'],
                'fields_by_model' => [
                    'gpt-image-1.5' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '2:3', '3:2'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'quality' => [
                            'enum' => ['medium', 'high'],
                            'required' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'gpt-image/text-to-image' => [
                'models' => ['gpt-image-1.5'],
                'fields_by_model' => [
                    'gpt-image-1.5' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '2:3', '3:2'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'quality' => [
                            'enum' => ['medium', 'high'],
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'grok-imagine/edit-image' => [
                'models' => ['grok-imagine-edit-image'],
                'fields_by_model' => [
                    'grok-imagine-edit-image' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'grok-imagine/extend' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'extension_duration_seconds' => [
                            'enum' => [6, 10],
                            'type' => 'integer',
                        ],
                        'start_seconds' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'grok-imagine/image-to-video' => [
                'models' => ['grok-imagine-image-to-video', 'grok-imagine-video-1.5-fast', 'grok-imagine-video-1.5-preview'],
                'fields_by_model' => [
                    'grok-imagine-image-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['2:3', '3:2', '1:1', '16:9', '9:16'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'index' => [
                            'type' => 'integer',
                        ],
                        'motion_style' => [
                            'enum' => ['fun', 'normal', 'spicy'],
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                    ],
                    'grok-imagine-video-1.5-fast' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:2', '2:3'],
                        ],
                        'duration_seconds' => [
                            'min' => 1,
                            'max' => 30,
                            'type' => 'integer',
                        ],
                        'index' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'grok-imagine-video-1.5-preview' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:2', '2:3', 'auto'],
                        ],
                        'duration_seconds' => [
                            'min' => 1,
                            'max' => 15,
                            'type' => 'integer',
                        ],
                        'index' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'prompt' => [
                            'min' => 1,
                            'max' => 4096,
                            'length' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'grok-imagine-image-to-video',
                    ],
                    'forbidden' => ['reference_image_urls'],
                ], [
                    'when' => [
                        'model' => 'grok-imagine-video-1.5-fast',
                    ],
                    'forbidden' => ['source_task_id', 'index', 'motion_style', 'enable_safety_checker'],
                ], [
                    'when' => [
                        'model' => 'grok-imagine-video-1.5-preview',
                    ],
                    'forbidden' => ['source_task_id', 'index', 'reference_image_urls', 'motion_style', 'enable_safety_checker'],
                ]],
            ],
            'grok-imagine/text-to-image' => [
                'models' => ['grok-imagine-text-to-image'],
                'fields_by_model' => [
                    'grok-imagine-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['2:3', '3:2', '1:1', '16:9', '9:16'],
                        ],
                    ],
                ],
            ],
            'grok-imagine/text-to-video' => [
                'models' => ['grok-imagine-text-to-video', 'grok-imagine-video-1.5-fast', 'grok-imagine-video-1.5-preview'],
                'fields_by_model' => [
                    'grok-imagine-text-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['2:3', '3:2', '1:1', '16:9', '9:16'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'motion_style' => [
                            'enum' => ['fun', 'normal', 'spicy'],
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                    ],
                    'grok-imagine-video-1.5-fast' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:2', '2:3'],
                        ],
                        'duration_seconds' => [
                            'min' => 1,
                            'max' => 30,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'grok-imagine-video-1.5-preview' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:2', '2:3', 'auto'],
                        ],
                        'duration_seconds' => [
                            'min' => 1,
                            'max' => 15,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 4096,
                            'length' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'grok-imagine-text-to-video',
                    ],
                    'forbidden' => ['reference_image_urls'],
                ], [
                    'when' => [
                        'model' => 'grok-imagine-video-1.5-fast',
                    ],
                    'forbidden' => ['motion_style', 'enable_safety_checker'],
                ], [
                    'when' => [
                        'model' => 'grok-imagine-video-1.5-preview',
                    ],
                    'forbidden' => ['reference_image_urls', 'motion_style', 'enable_safety_checker'],
                ]],
            ],
            'grok-imagine/upscale-image' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'hailuo/image-to-video' => [
                'models' => ['hailuo-02-image-to-video-pro', 'hailuo-02-image-to-video-standard', 'hailuo-2.3-image-to-video-pro', 'hailuo-2.3-image-to-video-standard'],
                'fields_by_model' => [
                    'hailuo-02-image-to-video-pro' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'hailuo-02-image-to-video-standard' => [
                        'duration_seconds' => [
                            'enum' => [6, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['512p', '768p'],
                        ],
                    ],
                    'hailuo-2.3-image-to-video-pro' => [
                        'duration_seconds' => [
                            'enum' => [6, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['768p', '1080p'],
                        ],
                    ],
                    'hailuo-2.3-image-to-video-standard' => [
                        'duration_seconds' => [
                            'enum' => [6, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['768p', '1080p'],
                        ],
                    ],
                ],
            ],
            'hailuo/text-to-video' => [
                'models' => ['hailuo-02-text-to-video-pro', 'hailuo-02-text-to-video-standard'],
                'fields_by_model' => [
                    'hailuo-02-text-to-video-pro' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                    ],
                    'hailuo-02-text-to-video-standard' => [
                        'duration_seconds' => [
                            'enum' => [6, 10],
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'happyhorse/edit-video' => [
                'models' => ['happyhorse-edit-video'],
                'fields_by_model' => [
                    'happyhorse-edit-video' => [
                        'audio_setting' => [
                            'enum' => ['auto', 'original'],
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'reference_image_urls' => [
                            'max_items' => 5,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_video_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'happyhorse/image-to-video' => [
                'models' => ['happyhorse-image-to-video'],
                'fields_by_model' => [
                    'happyhorse-image-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'happyhorse/text-to-video' => [
                'models' => ['happyhorse-character', 'happyhorse-text-to-video'],
                'fields_by_model' => [
                    'happyhorse-character' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1', '4:3', '3:4'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'reference_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 9,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'happyhorse-text-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1', '4:3', '3:4'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'ideogram-v3/edit-image' => [
                'models' => ['ideogram-v3-character-edit', 'ideogram-v3-edit'],
                'fields_by_model' => [
                    'ideogram-v3-character-edit' => [
                        'mask_url' => [
                            'required' => true,
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'reference_image_urls' => [
                            'required' => true,
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                        'style' => [
                            'enum' => ['auto', 'realistic', 'fiction'],
                        ],
                    ],
                    'ideogram-v3-edit' => [
                        'mask_url' => [
                            'required' => true,
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'ideogram-v3/reframe-image' => [
                'models' => ['ideogram-v3-reframe'],
                'fields_by_model' => [
                    'ideogram-v3-reframe' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '9:16', '4:3', '16:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                        'style' => [
                            'enum' => ['auto', 'general', 'realistic', 'design'],
                        ],
                    ],
                ],
            ],
            'ideogram-v3/remix-image' => [
                'models' => ['ideogram-v3-character-remix', 'ideogram-v3-remix'],
                'fields_by_model' => [
                    'ideogram-v3-character-remix' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '9:16', '4:3', '16:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'reference_image_urls' => [
                            'required' => true,
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                        'style' => [
                            'enum' => ['auto', 'realistic', 'fiction'],
                        ],
                    ],
                    'ideogram-v3-remix' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '9:16', '4:3', '16:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                        'style' => [
                            'enum' => ['auto', 'general', 'realistic', 'design'],
                        ],
                    ],
                ],
            ],
            'ideogram-v3/text-to-image' => [
                'models' => ['ideogram-v3-character', 'ideogram-v3-text-to-image'],
                'fields_by_model' => [
                    'ideogram-v3-character' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '9:16', '4:3', '16:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'reference_image_urls' => [
                            'required' => true,
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'style' => [
                            'enum' => ['auto', 'realistic', 'fiction'],
                        ],
                    ],
                    'ideogram-v3-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '9:16', '4:3', '16:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4],
                            'type' => 'integer',
                        ],
                        'rendering_speed' => [
                            'enum' => ['turbo', 'balanced', 'quality'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'style' => [
                            'enum' => ['auto', 'general', 'realistic', 'design'],
                        ],
                    ],
                ],
            ],
            'imagen-4/remix-image' => [
                'models' => ['imagen-4-pro-remix-image'],
                'fields_by_model' => [
                    'imagen-4-pro-remix-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '2:3', '3:2', '3:4', '4:3', '4:5', '5:4', '9:16', '16:9', '21:9', 'auto'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpg'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 8,
                        ],
                    ],
                ],
            ],
            'imagen-4/text-to-image' => [
                'models' => ['imagen-4', 'imagen-4-fast', 'imagen-4-ultra'],
                'fields_by_model' => [
                    'imagen-4' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:4', '4:3'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'imagen-4-fast' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:4', '4:3', 'auto'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'imagen-4-ultra' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '9:16', '3:4', '4:3'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'infinitetalk/audio-to-video' => [
                'models' => ['infinitetalk-from-audio'],
                'fields_by_model' => [
                    'infinitetalk-from-audio' => [
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'kling/avatar' => [
                'models' => ['kling-ai-avatar-pro', 'kling-ai-avatar-standard', 'kling-ai-avatar-v1-pro', 'kling-v1-avatar-standard'],
                'fields_by_model' => [
                    'kling-ai-avatar-pro' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'kling-ai-avatar-standard' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'kling-ai-avatar-v1-pro' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v1-avatar-standard' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'kling/image-to-video' => [
                'models' => ['kling-v2.1-master-image-to-video', 'kling-v2.1-pro', 'kling-v2.1-standard', 'kling-v2.5-turbo-image-to-video-pro', 'kling-v3-turbo-image-to-video'],
                'fields_by_model' => [
                    'kling-v2.1-master-image-to-video' => [
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v2.1-pro' => [
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v2.1-standard' => [
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v2.5-turbo-image-to-video-pro' => [
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v3-turbo-image-to-video' => [
                        'duration_seconds' => [
                            'enum' => [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 2500,
                            'length' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'kling-v2.1-master-image-to-video',
                    ],
                    'forbidden' => ['output_resolution'],
                ], [
                    'when' => [
                        'model' => 'kling-v2.1-pro',
                    ],
                    'forbidden' => ['output_resolution'],
                ], [
                    'when' => [
                        'model' => 'kling-v2.1-standard',
                    ],
                    'forbidden' => ['output_resolution'],
                ], [
                    'when' => [
                        'model' => 'kling-v2.5-turbo-image-to-video-pro',
                    ],
                    'forbidden' => ['output_resolution'],
                ], [
                    'when' => [
                        'model' => 'kling-v3-turbo-image-to-video',
                    ],
                    'forbidden' => ['aspect_ratio', 'negative_prompt', 'cfg_scale', 'last_frame_image_url'],
                ]],
            ],
            'kling/motion-control' => [
                'models' => ['kling-3.0'],
                'fields_by_model' => [
                    'kling-3.0' => [
                        'background_source' => [
                            'enum' => ['video', 'image'],
                        ],
                        'character_orientation' => [
                            'enum' => ['video', 'image'],
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'reference_video_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'kling/text-to-video' => [
                'models' => ['kling-3.0', 'kling-v2.1-master-text-to-video', 'kling-v2.5-turbo-text-to-video-pro', 'kling-v3-turbo-text-to-video'],
                'fields_by_model' => [
                    'kling-3.0' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1'],
                        ],
                        'duration_seconds' => [
                            'enum' => [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p', '4k'],
                        ],
                    ],
                    'kling-v2.1-master-text-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1'],
                        ],
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v2.5-turbo-text-to-video-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1'],
                        ],
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                    ],
                    'kling-v3-turbo-text-to-video' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1'],
                        ],
                        'duration_seconds' => [
                            'enum' => [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 2500,
                            'length' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'kling-v3-turbo-text-to-video',
                    ],
                    'forbidden' => ['enable_sound', 'negative_prompt', 'cfg_scale', 'multi_shots', 'multi_prompt', 'first_frame_image_url', 'last_frame_image_url', 'kling_elements'],
                ]],
            ],
            'luma/modify-video' => [
                'models' => ['luma-modify-video'],
                'fields_by_model' => [
                    'luma-modify-video' => [
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_video_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/edit-image' => [
                'models' => ['midjourney-edit-image'],
                'fields_by_model' => [
                    'midjourney-edit-image' => [
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/get-seed' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'image_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/image-to-prompt' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/image-to-video' => [
                'models' => ['midjourney-image-to-video'],
                'fields_by_model' => [
                    'midjourney-image-to-video' => [
                        'output_resolution' => [
                            'enum' => ['480p'],
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/shorten-prompt' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'midjourney/text-to-image' => [
                'models' => ['midjourney-v8.1'],
                'fields_by_model' => [
                    'midjourney-v8.1' => [
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'nano-banana/edit-image' => [
                'models' => ['nano-banana-2-lite', 'nano-banana-edit'],
                'fields_by_model' => [
                    'nano-banana-2-lite' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '1:4', '1:8', '2:3', '3:2', '3:4', '4:1', '4:3', '4:5', '5:4', '8:1', '9:16', '16:9', '21:9', 'auto'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 20000,
                            'length' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 10,
                        ],
                    ],
                    'nano-banana-edit' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '9:16', '16:9', '3:4', '4:3', '3:2', '2:3', '5:4', '4:5', '21:9', 'auto'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'source_image_urls' => [
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'nano-banana-2-lite',
                    ],
                    'forbidden' => ['output_format'],
                ]],
            ],
            'nano-banana/text-to-image' => [
                'models' => ['nano-banana', 'nano-banana-2', 'nano-banana-2-lite', 'nano-banana-pro'],
                'fields_by_model' => [
                    'nano-banana' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '9:16', '16:9', '3:4', '4:3', '3:2', '2:3', '5:4', '4:5', '21:9', 'auto'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg', 'jpg'],
                        ],
                    ],
                    'nano-banana-2' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '1:4', '1:8', '2:3', '3:2', '3:4', '4:1', '4:3', '4:5', '5:4', '8:1', '9:16', '16:9', '21:9', 'auto'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg', 'jpg'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                    ],
                    'nano-banana-2-lite' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '1:4', '1:8', '2:3', '3:2', '3:4', '4:1', '4:3', '4:5', '5:4', '8:1', '9:16', '16:9', '21:9', 'auto'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 20000,
                            'length' => true,
                        ],
                        'reference_image_urls' => [
                            'max_items' => 10,
                        ],
                    ],
                    'nano-banana-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '2:3', '3:2', '3:4', '4:3', '4:5', '5:4', '9:16', '16:9', '21:9', 'auto'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg', 'jpg'],
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'nano-banana-2-lite',
                    ],
                    'forbidden' => ['output_resolution', 'output_format'],
                ]],
            ],
            'omnihuman/audio-to-video' => [
                'models' => ['omnihuman-1.5'],
                'fields_by_model' => [
                    'omnihuman-1.5' => [
                        'mask_urls' => [
                            'max_items' => 5,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'max' => 1000,
                            'length' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'omnihuman/human-identification' => [
                'models' => ['omnihuman-1.5-human-identification'],
                'fields_by_model' => [
                    'omnihuman-1.5-human-identification' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'omnihuman/subject-detection' => [
                'models' => ['omnihuman-1.5-subject-detection'],
                'fields_by_model' => [
                    'omnihuman-1.5-subject-detection' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'openai-tts/text-to-speech' => [
                'models' => ['tts-1', 'tts-1-hd'],
                'fields_by_model' => [
                    'tts-1' => [
                        'text' => [
                            'required' => true,
                            'max' => 4096,
                            'length' => true,
                        ],
                    ],
                    'tts-1-hd' => [
                        'text' => [
                            'required' => true,
                            'max' => 4096,
                            'length' => true,
                        ],
                    ],
                ],
            ],
            'producer/text-to-music' => [
                'models' => ['fuzz-2.0'],
                'fields_by_model' => [
                    'fuzz-2.0' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 200,
                            'length' => true,
                        ],
                        'vocal_mode' => [
                            'enum' => ['exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'vocal_mode' => 'exact_lyrics',
                    ],
                    'required' => ['lyrics'],
                ], [
                    'when' => [
                        'vocal_mode' => 'instrumental',
                    ],
                    'forbidden' => ['lyrics'],
                ]],
            ],
            'qwen-2/edit-image' => [
                'models' => ['qwen-2-edit-image'],
                'fields_by_model' => [
                    'qwen-2-edit-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '2:3', '3:2', '3:4', '4:3', '9:16', '16:9', '21:9'],
                        ],
                        'output_format' => [
                            'enum' => ['jpeg', 'png'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'qwen-2/remix-image' => [
                'models' => ['qwen-2-remix-image'],
                'fields_by_model' => [
                    'qwen-2-remix-image' => [
                        'acceleration' => [
                            'enum' => ['none', 'regular', 'high'],
                        ],
                        'num_inference_steps' => [
                            'type' => 'integer',
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'qwen-2/text-to-image' => [
                'models' => ['qwen-2-text-to-image'],
                'fields_by_model' => [
                    'qwen-2-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '3:4', '4:3', '9:16', '16:9'],
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'recraft/remove-background' => [
                'models' => ['recraft-remove-background'],
                'fields_by_model' => [
                    'recraft-remove-background' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'recraft/upscale-image' => [
                'models' => ['recraft-crisp-upscale'],
                'fields_by_model' => [
                    'recraft-crisp-upscale' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'runway-aleph/edit-video' => [
                'models' => ['runway-aleph'],
                'fields_by_model' => [
                    'runway-aleph' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '4:3', '3:4', '1:1', '21:9'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_video_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'runway/extend-video' => [
                'models' => ['runway'],
                'fields_by_model' => [
                    'runway' => [
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'source_task_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'runway/text-to-video' => [
                'models' => ['runway'],
                'fields_by_model' => [
                    'runway' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1', '4:3', '3:4'],
                        ],
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'seedance/text-to-video' => [
                'models' => ['seedance-1.5-pro', 'seedance-2-mini', 'seedance-2.0', 'seedance-2.0-fast', 'seedance-v1-lite', 'seedance-v1-pro', 'seedance-v1-pro-fast'],
                'fields_by_model' => [
                    'seedance-1.5-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '21:9'],
                        ],
                        'duration_seconds' => [
                            'required' => true,
                            'min' => 4,
                            'max' => 12,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p', '1080p'],
                        ],
                        'source_image_urls' => [
                            'max_items' => 2,
                        ],
                    ],
                    'seedance-2-mini' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '21:9', 'auto'],
                        ],
                        'duration_seconds' => [
                            'min' => 4,
                            'max' => 15,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'reference_audio_urls' => [
                            'max_items' => 3,
                        ],
                        'reference_image_urls' => [
                            'max_items' => 9,
                        ],
                        'reference_video_urls' => [
                            'max_items' => 3,
                        ],
                    ],
                    'seedance-2.0' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '21:9', 'auto'],
                        ],
                        'duration_seconds' => [
                            'min' => 4,
                            'max' => 15,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p', '1080p', '4k'],
                        ],
                        'reference_audio_urls' => [
                            'max_items' => 3,
                        ],
                        'reference_image_urls' => [
                            'max_items' => 9,
                        ],
                        'reference_video_urls' => [
                            'max_items' => 3,
                        ],
                    ],
                    'seedance-2.0-fast' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '21:9', 'auto'],
                        ],
                        'duration_seconds' => [
                            'min' => 4,
                            'max' => 15,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'reference_audio_urls' => [
                            'max_items' => 3,
                        ],
                        'reference_image_urls' => [
                            'max_items' => 9,
                        ],
                        'reference_video_urls' => [
                            'max_items' => 3,
                        ],
                    ],
                    'seedance-v1-lite' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '9:21'],
                        ],
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'seedance-v1-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '21:9'],
                        ],
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'seedance-v1-pro-fast' => [
                        'duration_seconds' => [
                            'enum' => [5, 10],
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                    ],
                ],
            ],
            'seedream/edit-image' => [
                'models' => ['seedream-4.5-edit', 'seedream-5-lite-edit', 'seedream-5-pro-edit', 'seedream-v4-edit'],
                'fields_by_model' => [
                    'seedream-4.5-edit' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 14,
                        ],
                    ],
                    'seedream-5-lite-edit' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 14,
                        ],
                    ],
                    'seedream-5-pro-edit' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 3,
                            'max' => 5000,
                            'length' => true,
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 10,
                        ],
                    ],
                    'seedream-v4-edit' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '3:2', '2:3', '16:9', '9:16', '21:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4, 5, 6],
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_image_urls' => [
                            'required' => true,
                            'min_items' => 1,
                            'max_items' => 10,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'seedream-4.5-edit',
                    ],
                    'forbidden' => ['output_format'],
                ], [
                    'when' => [
                        'model' => 'seedream-5-pro-edit',
                    ],
                    'forbidden' => ['output_resolution', 'output_count', 'seed'],
                ], [
                    'when' => [
                        'model' => 'seedream-v4-edit',
                    ],
                    'forbidden' => ['output_format'],
                ]],
            ],
            'seedream/text-to-image' => [
                'models' => ['seedream-4.5-text-to-image', 'seedream-5-lite-text-to-image', 'seedream-5-pro-text-to-image', 'seedream-v4-text-to-image'],
                'fields_by_model' => [
                    'seedream-4.5-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'seedream-5-lite-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'seedream-5-pro-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16', '2:3', '3:2', '21:9'],
                            'required' => true,
                        ],
                        'output_format' => [
                            'enum' => ['png', 'jpeg'],
                        ],
                        'output_quality' => [
                            'enum' => ['basic', 'high'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 3,
                            'max' => 5000,
                            'length' => true,
                        ],
                    ],
                    'seedream-v4-text-to-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '3:2', '2:3', '16:9', '9:16', '21:9'],
                        ],
                        'output_count' => [
                            'enum' => [1, 2, 3, 4, 5, 6],
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'seedream-4.5-text-to-image',
                    ],
                    'forbidden' => ['output_format'],
                ], [
                    'when' => [
                        'model' => 'seedream-5-pro-text-to-image',
                    ],
                    'forbidden' => ['output_resolution', 'output_count', 'seed'],
                ], [
                    'when' => [
                        'model' => 'seedream-v4-text-to-image',
                    ],
                    'forbidden' => ['output_format'],
                ]],
            ],
            'suno/add-instrumental' => [
                'models' => ['suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v4.5-plus' => [
                        'model' => [
                            'required' => true,
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v5.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                ],
            ],
            'suno/add-vocals' => [
                'models' => ['suno-v4.5-plus', 'suno-v5'],
                'fields_by_model' => [
                    'suno-v4.5-plus' => [
                        'lyrics' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v5' => [
                        'lyrics' => [
                            'required' => true,
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                ],
            ],
            'suno/boost-style' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/check-voice' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'task_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'suno/convert-audio' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/cover-audio' => [
                'models' => ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v4' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-all' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-plus' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url' => [
                            'required' => true,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'vocal_mode' => 'auto_lyrics',
                    ],
                    'required' => ['prompt'],
                    'forbidden' => ['lyrics', 'style', 'title'],
                ], [
                    'when' => [
                        'vocal_mode' => 'exact_lyrics',
                    ],
                    'required' => ['lyrics', 'style', 'title'],
                    'forbidden' => ['prompt'],
                ], [
                    'when' => [
                        'vocal_mode' => 'instrumental',
                    ],
                    'required' => ['style', 'title'],
                    'forbidden' => ['prompt', 'lyrics'],
                ]],
            ],
            'suno/create-mashup' => [
                'models' => ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v4' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-all' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-plus' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'upload_url_list' => [
                            'required' => true,
                            'min_items' => 2,
                            'max_items' => 2,
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'vocal_mode' => 'auto_lyrics',
                    ],
                    'required' => ['prompt'],
                    'forbidden' => ['lyrics', 'style', 'title'],
                ], [
                    'when' => [
                        'vocal_mode' => 'exact_lyrics',
                    ],
                    'required' => ['lyrics', 'style', 'title'],
                    'forbidden' => ['prompt'],
                ], [
                    'when' => [
                        'vocal_mode' => 'instrumental',
                    ],
                    'required' => ['style', 'title'],
                    'forbidden' => ['prompt', 'lyrics'],
                ]],
            ],
            'suno/extend-music' => [
                'models' => ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v4' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v4.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v4.5-all' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v4.5-plus' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                    'suno-v5.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'parameter_mode' => [
                            'enum' => ['source', 'custom'],
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                    ],
                ],
            ],
            'suno/generate-artwork' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'task_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'suno/generate-lyrics' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/generate-midi' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/generate-persona' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/generate-voice' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'singer_skill_level' => [
                            'enum' => ['beginner', 'intermediate', 'advanced', 'professional'],
                        ],
                        'task_id' => [
                            'required' => true,
                        ],
                        'verify_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'suno/get-timestamped-lyrics' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/regenerate-validation-phrase' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'task_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'suno/replace-section' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'full_lyrics' => [
                            'required' => true,
                        ],
                        'infill_end_time' => [
                            'required' => true,
                        ],
                        'infill_start_time' => [
                            'required' => true,
                        ],
                        'lyrics' => [
                            'required' => true,
                        ],
                        'model' => [
                            'enum' => ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                        ],
                        'tags' => [
                            'required' => true,
                        ],
                        'title' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'suno/separate-audio-stems' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'audio_id' => [
                            'required' => true,
                        ],
                        'stem_name' => [
                            'enum' => ['Lead Vocal', 'Drum Kit', 'Kick', 'Snare', 'Risers', 'Bass', 'Backing Vocals', 'Piano', 'Electric Guitar', 'Percussion', 'String Section', 'Synth', 'Acoustic Guitar', 'Sound Effects', 'Synth Pad', 'Synth Bass', 'Guitar', 'Brass Section', 'Organ', 'Electronic Drum Kit', 'Lead Electric Guitar', 'Synth Keys', 'Rhythm Electric Guitar', 'Electric Piano', 'Upright Bass', 'Keyboards', 'Distorted Electric Guitar', 'Synth Strings', 'Synth Lead', 'Woodwinds', 'Rhythm Acoustic Guitar', 'Flute', 'Harp', 'Tambourine', 'Trumpet', 'Arpeggiator', 'Accordion', 'Fiddle', 'Pedal Steel Guitar', 'Synth Voice', 'Violin', 'Digital Piano', 'Synth Brass', 'Mandolin', 'Choir', 'Banjo', 'Bells', 'Clarinet', 'Tenor Saxophone', 'Trombone', 'Shaker', 'French Horn', 'Glockenspiel', 'Electric Bass', 'Cello', 'Timpani', 'Harmonica', 'Marimba', 'Vibraphone', 'Lap Steel Guitar', 'Saxophone', 'Orchestra', 'Horns', 'Cymbals', 'Hand Clap', 'Oboe', 'Celesta', 'Congas', 'Drone', 'Alto Saxophone', 'Double Bass', 'Ukulele', 'Harpsichord', 'Baritone Saxophone', 'Xylophone', 'Tuba', 'Bass Guitar', 'Whistle', 'Lead Guitar', 'Rhodes', '808', 'Bongos', 'Bassoon', 'Cowbell', 'Viola', 'Sitar', 'Steel Drums', 'Piccolo', 'Theremin', 'Bagpipes', 'Hi-Hat', 'Music Box', 'Melodica', 'Tabla', 'Koto', 'Djembe', 'Taiko', 'Didgeridoo'],
                        ],
                        'task_id' => [
                            'required' => true,
                        ],
                        'type' => [
                            'enum' => ['separate_vocal', 'split_stem', 'split_stem_advanced'],
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'type' => 'split_stem_advanced',
                    ],
                    'required' => ['stem_name'],
                ]],
            ],
            'suno/text-to-music' => [
                'models' => ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v4' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-all' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v4.5-plus' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                    'suno-v5.5' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'model' => [
                            'required' => true,
                        ],
                        'persona_type' => [
                            'enum' => ['style', 'voice'],
                        ],
                        'vocal_gender' => [
                            'enum' => ['male', 'female'],
                        ],
                        'vocal_mode' => [
                            'enum' => ['auto_lyrics', 'exact_lyrics', 'instrumental'],
                            'required' => true,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'vocal_mode' => 'auto_lyrics',
                    ],
                    'required' => ['prompt'],
                    'forbidden' => ['lyrics', 'style', 'title'],
                ], [
                    'when' => [
                        'vocal_mode' => 'exact_lyrics',
                    ],
                    'required' => ['lyrics', 'style', 'title'],
                    'forbidden' => ['prompt'],
                ], [
                    'when' => [
                        'vocal_mode' => 'instrumental',
                    ],
                    'required' => ['style', 'title'],
                    'forbidden' => ['prompt', 'lyrics'],
                ]],
            ],
            'suno/text-to-sound' => [
                'models' => ['suno-v5', 'suno-v5.5'],
                'fields_by_model' => [
                    'suno-v5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'sound_key' => [
                            'enum' => ['Cm', 'C#m', 'Dm', 'D#m', 'Em', 'Fm', 'F#m', 'Gm', 'G#m', 'Am', 'A#m', 'Bm', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'],
                        ],
                        'sound_tempo' => [
                            'type' => 'integer',
                        ],
                    ],
                    'suno-v5.5' => [
                        'model' => [
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'sound_key' => [
                            'enum' => ['Cm', 'C#m', 'Dm', 'D#m', 'Em', 'Fm', 'F#m', 'Gm', 'G#m', 'Am', 'A#m', 'Bm', 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'],
                        ],
                        'sound_tempo' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'suno/visualize-music' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [],
                ],
            ],
            'suno/voice-to-validation-phrase' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'language' => [
                            'enum' => ['en', 'zh', 'es', 'fr', 'pt', 'de', 'ja', 'ko', 'hi', 'ru'],
                        ],
                        'vocal_end_seconds' => [
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'vocal_start_seconds' => [
                            'required' => true,
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'topaz/upscale-image' => [
                'models' => ['topaz-upscale-image'],
                'fields_by_model' => [
                    'topaz-upscale-image' => [
                        'source_image_url' => [
                            'required' => true,
                        ],
                        'upscale_factor' => [
                            'enum' => [1, 2, 4, 8],
                            'required' => true,
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'topaz/upscale-video' => [
                'models' => ['topaz-upscale-video'],
                'fields_by_model' => [
                    'topaz-upscale-video' => [
                        'source_video_url' => [
                            'required' => true,
                        ],
                        'upscale_factor' => [
                            'enum' => [1, 2, 4],
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'veo-3-1/extend-video' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'seeds' => [
                            'type' => 'integer',
                        ],
                        'source_task_id' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'veo-3-1/text-to-video' => [
                'models' => ['veo-3.1', 'veo-3.1-fast'],
                'fields_by_model' => [
                    'veo-3.1' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', 'auto'],
                        ],
                        'duration_seconds' => [
                            'enum' => [4, 6, 8],
                            'type' => 'integer',
                        ],
                        'input_mode' => [
                            'enum' => ['text', 'first_and_last_frames', 'reference'],
                        ],
                        'reference_image_urls' => [
                            'min_items' => 1,
                            'max_items' => 3,
                        ],
                        'seeds' => [
                            'type' => 'integer',
                        ],
                    ],
                    'veo-3.1-fast' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', 'auto'],
                        ],
                        'duration_seconds' => [
                            'enum' => [4, 6, 8],
                            'type' => 'integer',
                        ],
                        'input_mode' => [
                            'enum' => ['text', 'first_and_last_frames', 'reference'],
                        ],
                        'reference_image_urls' => [
                            'min_items' => 1,
                            'max_items' => 3,
                        ],
                        'seeds' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'veo-3-1/upscale-video' => [
                'models' => [],
                'fields_by_model' => [
                    '_' => [
                        'index' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['1080p', '4k'],
                        ],
                    ],
                ],
            ],
            'volcengine-lip-sync/lip-sync-video' => [
                'models' => ['volcengine-lip-sync'],
                'fields_by_model' => [
                    'volcengine-lip-sync' => [
                        'mode' => [
                            'enum' => ['lite', 'basic'],
                            'required' => true,
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_video_url' => [
                            'required' => true,
                        ],
                        'template_start_seconds' => [
                            'min' => 0,
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'mode' => 'lite',
                    ],
                    'forbidden' => ['enable_scene_detection'],
                ], [
                    'when' => [
                        'mode' => 'basic',
                    ],
                    'forbidden' => ['align_audio', 'align_audio_reverse', 'template_start_seconds'],
                ], [
                    'when' => [
                        'align_audio_reverse' => true,
                    ],
                    'required' => ['align_audio'],
                ]],
            ],
            'wan/animate' => [
                'models' => ['wan-2.2-animate-move', 'wan-2.2-animate-replace'],
                'fields_by_model' => [
                    'wan-2.2-animate-move' => [
                        'output_resolution' => [
                            'enum' => ['480p', '580p', '720p'],
                        ],
                        'reference_video_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                    'wan-2.2-animate-replace' => [
                        'output_resolution' => [
                            'enum' => ['480p', '580p', '720p'],
                        ],
                        'reference_video_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'wan/edit-video' => [
                'models' => ['wan-2.6-edit-video', 'wan-2.6-flash-edit-video', 'wan-2.7-edit-video'],
                'fields_by_model' => [
                    'wan-2.6-edit-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_video_urls' => [
                            'required' => true,
                        ],
                    ],
                    'wan-2.6-flash-edit-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_video_urls' => [
                            'required' => true,
                        ],
                    ],
                    'wan-2.7-edit-video' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1', '4:3', '3:4'],
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_video_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'wan/image-to-video' => [
                'models' => ['wan-2.2-a14b-image-to-video-turbo', 'wan-2.5-image-to-video', 'wan-2.6-flash-image-to-video', 'wan-2.6-image-to-video', 'wan-2.7-image-to-video'],
                'fields_by_model' => [
                    'wan-2.2-a14b-image-to-video-turbo' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '720p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.5-image-to-video' => [
                        'duration_seconds' => [
                            'required' => true,
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.6-flash-image-to-video' => [
                        'audio' => [
                            'required' => true,
                        ],
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'wan-2.6-image-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'first_frame_image_url' => [
                            'required' => true,
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                    ],
                    'wan-2.7-image-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'wan-2.6-flash-image-to-video',
                    ],
                    'forbidden' => ['seed'],
                ], [
                    'when' => [
                        'model' => 'wan-2.6-image-to-video',
                    ],
                    'forbidden' => ['seed'],
                ]],
            ],
            'wan/speech-to-video' => [
                'models' => ['wan-2.2-a14b-speech-to-video-turbo'],
                'fields_by_model' => [
                    'wan-2.2-a14b-speech-to-video-turbo' => [
                        'frames_per_second' => [
                            'type' => 'integer',
                        ],
                        'num_frames' => [
                            'type' => 'integer',
                        ],
                        'num_inference_steps' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '580p', '720p'],
                        ],
                        'prompt' => [
                            'required' => true,
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                        'source_audio_url' => [
                            'required' => true,
                        ],
                        'source_image_url' => [
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'wan/text-to-image' => [
                'models' => ['wan-2.7-image', 'wan-2.7-image-pro'],
                'fields_by_model' => [
                    'wan-2.7-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '4:3', '21:9', '3:4', '9:16', '8:1', '1:8'],
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.7-image-pro' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '16:9', '4:3', '21:9', '3:4', '9:16', '8:1', '1:8'],
                        ],
                        'output_count' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['1k', '2k', '4k'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
            'wan/text-to-video' => [
                'models' => ['wan-2.2-a14b-text-to-video-turbo', 'wan-2.5-text-to-video', 'wan-2.6-text-to-video', 'wan-2.7-r2v', 'wan-2.7-text-to-video'],
                'fields_by_model' => [
                    'wan-2.2-a14b-text-to-video-turbo' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['480p', '580p', '720p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.5-text-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.6-text-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                    ],
                    'wan-2.7-r2v' => [
                        'aspect_ratio' => [
                            'enum' => ['16:9', '9:16', '1:1', '4:3', '3:4'],
                        ],
                        'duration_seconds' => [
                            'min' => 2,
                            'max' => 10,
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                    'wan-2.7-text-to-video' => [
                        'duration_seconds' => [
                            'type' => 'integer',
                        ],
                        'output_resolution' => [
                            'enum' => ['720p', '1080p'],
                        ],
                        'seed' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
                'rules' => [[
                    'when' => [
                        'model' => 'wan-2.6-text-to-video',
                    ],
                    'forbidden' => ['seed'],
                ]],
            ],
            'z-image/text-to-image' => [
                'models' => ['z-image'],
                'fields_by_model' => [
                    'z-image' => [
                        'aspect_ratio' => [
                            'enum' => ['1:1', '4:3', '3:4', '16:9', '9:16'],
                            'required' => true,
                        ],
                        'prompt' => [
                            'required' => true,
                            'min' => 1,
                            'max' => 1000,
                            'length' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
