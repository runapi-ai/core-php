<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\TaskFailedException;
use RunApi\Core\Errors\TaskTimeoutException;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;

/**
 * Release smoke coverage for every public PHP SDK resource.
 */
final class AllResourcesOfflineSmokeTest extends TestCase
{
    private const IMAGE_URL = 'https://cdn.runapi.ai/public/samples/image.jpg';
    private const FIRST_FRAME_URL = 'https://cdn.runapi.ai/public/samples/first-frame.jpg';
    private const VIDEO_URL = 'https://cdn.runapi.ai/public/samples/video.mp4';
    private const AUDIO_URL = 'https://cdn.runapi.ai/public/samples/voice.mp3';
    private const PORTRAIT_URL = 'https://cdn.runapi.ai/public/samples/portrait.jpg';

    /**
     * @return iterable<string, array{ResourceCase}>
     */
    public static function resourceCases(): iterable
    {
        foreach (self::discoverResourceCases() as $case) {
            yield $case->package . '::' . $case->resource => [$case];
        }
    }

    /**
     * @return iterable<string, array{ResourceCase}>
     */
    public static function asyncResourceCases(): iterable
    {
        foreach (self::discoverResourceCases() as $case) {
            if ($case->type === 'async') {
                yield $case->package . '::' . $case->resource => [$case];
            }
        }
    }

    /**
     * @return iterable<string, array{ResourceCase}>
     */
    public static function syncResourceCases(): iterable
    {
        foreach (self::discoverResourceCases() as $case) {
            if ($case->type === 'sync') {
                yield $case->package . '::' . $case->resource => [$case];
            }
        }
    }

    public function testDiscoversExpectedWorkspaceCoverage(): void
    {
        $cases = self::discoverResourceCases();

        self::assertCount(99, $cases);
        self::assertCount(34, array_unique(array_map(static fn (ResourceCase $case): string => $case->package, $cases)));
    }

    public function testUniversalResourcesUseExpectedHttpBoundary(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"file_name":"sample.png","url":"https://file.runapi.ai/sample.png","size_bytes":12,"mime_type":"image/png","created_at":"2026-06-24T00:00:00Z","expires_at":"2026-06-25T00:00:00Z","extra_field":"kept"}'),
            new Response(200, [], '{"balance_cents":1000,"paid_balance_cents":900,"bonus_balance_cents":100,"spent_cents_today":10,"spent_cents_total":20,"extra_field":"kept"}'),
            new Response(200, [], '{"id":1,"name":"Jane Doe","email":"jane@runapi.ai","account":{"id":2,"name":"Acme"},"extra_field":"kept"}'),
        ]);
        $client = new UniversalSmokeClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $file = $client->files->create([
            'source' => [
                'type' => 'url',
                'url' => self::IMAGE_URL,
            ],
            'file_name' => 'sample.png',
        ]);
        $balance = $client->account->balance();
        $info = $client->account->info();

        self::assertSame('/api/v1/files', $transport->requests[0]->getUri()->getPath());
        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('application/json', $transport->requests[0]->getHeaderLine('Content-Type'));
        self::assertSame('/api/v1/me/balance', $transport->requests[1]->getUri()->getPath());
        self::assertSame('/api/v1/me', $transport->requests[2]->getUri()->getPath());
        self::assertSame('https://file.runapi.ai/sample.png', $file->url);
        self::assertSame('kept', $file->toArray()['extra_field']);
        self::assertSame(1000, $balance->balanceCents);
        self::assertSame('kept', $balance->toArray()['extra_field']);
        self::assertSame('Acme', $info->account->name);
        self::assertSame('kept', $info->toArray()['extra_field']);
    }

    #[DataProvider('asyncResourceCases')]
    public function testAsyncResourceCreateGetAndRun(ResourceCase $case): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"create_task","extra_field":"kept"}'),
            new Response(200, [], json_encode($this->taskPayload('get_task', 'processing', $case), JSON_THROW_ON_ERROR)),
            new Response(200, [], '{"id":"run_task"}'),
            new Response(200, [], json_encode($this->taskPayload('run_task', 'completed', $case, includeOutputs: true), JSON_THROW_ON_ERROR)),
        ]);
        $client = $this->client($case, $transport);
        $resource = $client->{$case->resource};

        $create = $resource->create($case->params + [
            'callback_url' => '',
            'null_option' => null,
            'empty_list' => [],
        ]);
        $get = $resource->get('get_task');
        $run = $resource->run($case->params, new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertInstanceOf(TaskCreateResponse::class, $create);
        self::assertInstanceOf(TaskResponse::class, $get);
        self::assertInstanceOf(TaskResponse::class, $run);
        self::assertSame('create_task', $create->id);
        self::assertSame('processing', $get->status);
        self::assertSame('completed', $run->status);
        self::assertSame('kept', $run->toArray()['extra_field']);

        self::assertCount(4, $transport->requests);
        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame($case->endpoint, $transport->requests[0]->getUri()->getPath());
        self::assertSame('GET', $transport->requests[1]->getMethod());
        self::assertSame($case->endpoint . '/get_task', $transport->requests[1]->getUri()->getPath());
        self::assertSame('POST', $transport->requests[2]->getMethod());
        self::assertSame($case->endpoint, $transport->requests[2]->getUri()->getPath());
        self::assertSame('GET', $transport->requests[3]->getMethod());
        self::assertSame($case->endpoint . '/run_task', $transport->requests[3]->getUri()->getPath());

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($body);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('null_option', $body);
        self::assertArrayNotHasKey('empty_list', $body);
    }

    #[DataProvider('syncResourceCases')]
    public function testSyncResourceRun(ResourceCase $case): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], json_encode($this->syncPayload($case), JSON_THROW_ON_ERROR)),
        ]);
        $client = $this->client($case, $transport);
        $resource = $client->{$case->resource};

        $run = $resource->run($case->params + [
            'callback_url' => '',
            'null_option' => null,
            'empty_list' => [],
        ]);

        self::assertInstanceOf(BaseModel::class, $run);
        self::assertSame('kept', $run->toArray()['extra_field']);
        self::assertCount(1, $transport->requests);
        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame($case->endpoint, $transport->requests[0]->getUri()->getPath());

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($body);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('null_option', $body);
        self::assertArrayNotHasKey('empty_list', $body);
    }

    public function testRepresentativeValidationFailuresStayClientSide(): void
    {
        $client = new \RunApi\ZImage\ZImageClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('prompt must be at most 1000 characters');

        $client->textToImage->create([
            'model' => 'z-image',
            'prompt' => str_repeat('a', 1001),
            'aspect_ratio' => '16:9',
        ]);
    }

    public function testRepresentativeAsyncFailureAndTimeoutExceptions(): void
    {
        $failedTransport = new QueueHttpClient([
            new Response(200, [], '{"id":"failed_task"}'),
            new Response(200, [], '{"id":"failed_task","status":"failed","error":"generation failed"}'),
        ]);
        $failedClient = new \RunApi\ZImage\ZImageClient(new ClientOptions(apiKey: 'k', httpClient: $failedTransport, maxRetries: 0));

        try {
            $failedClient->textToImage->run([
                'model' => 'z-image',
                'prompt' => 'A quiet studio scene',
                'aspect_ratio' => '16:9',
            ], new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));
            self::fail('Expected task failure exception.');
        } catch (TaskFailedException $exception) {
            self::assertSame('generation failed', $exception->getMessage());
        }

        $timeoutTransport = new QueueHttpClient([
            new Response(200, [], '{"id":"pending_task"}'),
            new Response(200, [], '{"id":"pending_task","status":"processing"}'),
        ]);
        $timeoutClient = new \RunApi\ZImage\ZImageClient(new ClientOptions(apiKey: 'k', httpClient: $timeoutTransport, maxRetries: 0));

        $this->expectException(TaskTimeoutException::class);
        $timeoutClient->textToImage->run([
            'model' => 'z-image',
            'prompt' => 'A quiet studio scene',
            'aspect_ratio' => '16:9',
        ], new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 0.0));
    }

    private function client(ResourceCase $case, QueueHttpClient $transport): BaseClient
    {
        $class = $case->clientClass;
        $client = new $class(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertInstanceOf(BaseClient::class, $client);

        return $client;
    }

    /**
     * @return array<string, mixed>
     */
    private function taskPayload(string $id, string $status, ResourceCase $case, bool $includeOutputs = false): array
    {
        $payload = [
            'id' => $id,
            'status' => $status,
            'extra_field' => 'kept',
        ];

        if (!$includeOutputs) {
            return $payload;
        }

        if ($case->outputKind === 'text') {
            $payload['text'] = 'transcript text';

            return $payload;
        }

        if ($case->outputKind === 'audio') {
            $payload['audios'] = [['url' => 'https://file.runapi.ai/result.mp3']];

            return $payload;
        }

        if ($case->outputKind === 'separated_audio') {
            $payload['separated_audios'] = [
                'vocal_url' => 'https://file.runapi.ai/vocal.mp3',
                'instrumental_url' => 'https://file.runapi.ai/instrumental.mp3',
            ];

            return $payload;
        }

        if ($case->outputKind === 'image') {
            $payload['images'] = [['url' => 'https://file.runapi.ai/result.png']];

            return $payload;
        }

        if ($case->outputKind === 'mask') {
            $payload['masks'] = [['url' => 'https://file.runapi.ai/mask.png']];

            return $payload;
        }

        if ($case->outputKind === 'subject_status') {
            $payload['subject_status'] = 1;

            return $payload;
        }

        $payload['videos'] = [['url' => 'https://file.runapi.ai/result.mp4']];

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function syncPayload(ResourceCase $case): array
    {
        $payload = [
            'id' => 'sync_result',
            'status' => 'completed',
            'extra_field' => 'kept',
        ];

        if ($case->resource === 'textToSpeech' && $case->outputKind === 'audio') {
            $payload['audios'] = [[
                'url' => 'https://file.runapi.ai/result.mp3',
                'format' => 'mp3',
                'mime_type' => 'audio/mpeg',
                'size_bytes' => 128,
            ]];
        } elseif ($case->resource === 'createAudio') {
            $payload['audio'] = [
                'id' => 'audio_1',
                'name' => 'Narrator',
            ];
        } elseif ($case->resource === 'createCharacter') {
            $payload['character'] = [
                'id' => 'character_1',
                'name' => 'Guide',
                'images' => [['url' => self::PORTRAIT_URL]],
            ];
        } elseif ($case->resource === 'generatePersona') {
            $payload['persona'] = [
                'id' => 'persona_1',
                'name' => 'Indie Pop',
            ];
        } elseif ($case->resource === 'getTimestampedLyrics') {
            $payload['lyrics'] = [
                [
                    'text' => 'hello',
                    'start_time' => 0.0,
                    'end_time' => 1.0,
                ],
            ];
        } elseif ($case->resource === 'boostStyle') {
            $payload['style'] = 'indie pop, warm drums';
        } elseif ($case->resource === 'checkVoice') {
            $payload['is_available'] = true;
        } elseif ($case->resource === 'getSeed') {
            $payload['seed'] = 8675309;
        } elseif (in_array($case->resource, ['imageToPrompt', 'shortenPrompt'], true)) {
            $payload['prompts'] = ['one', 'two', 'three', 'four'];
        }

        return $payload;
    }

    /**
     * @return list<ResourceCase>
     */
    private static function discoverResourceCases(): array
    {
        static $cases = null;

        if ($cases !== null) {
            return $cases;
        }

        $contract = self::contract();
        $cases = [];
        $packagesRoot = realpath(__DIR__ . '/../../..');
        if ($packagesRoot === false) {
            throw new \RuntimeException('Unable to locate PHP packages root.');
        }

        foreach (glob($packagesRoot . '/runapi-*/src/*Client.php') ?: [] as $clientFile) {
            $packageDir = basename(dirname(dirname($clientFile)));
            if ($packageDir === 'runapi-core') {
                continue;
            }

            $clientSource = (string) file_get_contents($clientFile);
            $namespace = self::match($clientSource, '/namespace ([^;]+);/');
            $clientName = self::match($clientSource, '/final class (\w+) /');
            $package = 'runapi-ai/' . substr($packageDir, strlen('runapi-'));

            preg_match_all('/public readonly (?!Files|Account)(\w+) \$(\w+);/', $clientSource, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $resourceClass = $namespace . '\\Resources\\' . $match[1];
                $resourceFile = dirname($clientFile) . '/Resources/' . $match[1] . '.php';
                $resourceSource = (string) file_get_contents($resourceFile);
                $endpoint = self::endpoint($resourceSource, $package, $match[2]);
                $action = self::action($resourceSource, $package, $endpoint);
                $type = str_contains($resourceSource, 'function create(') || str_contains($resourceSource, 'extends AudioResource') ? 'async' : 'sync';
                $outputKind = self::outputKind($resourceSource, $package);
                $params = self::paramsFor($action, $contract, $resourceSource, $package, $match[2]);

                $cases[] = new ResourceCase(
                    package: $package,
                    clientClass: $namespace . '\\' . $clientName,
                    resourceClass: $resourceClass,
                    resource: $match[2],
                    type: $type,
                    endpoint: $endpoint,
                    action: $action,
                    outputKind: $outputKind,
                    params: $params,
                );
            }
        }

        usort($cases, static fn (ResourceCase $a, ResourceCase $b): int => [$a->package, $a->resource] <=> [$b->package, $b->resource]);

        return $cases;
    }

    /**
     * @return array<string, mixed>
     */
    private static function contract(): array
    {
        return \RunApi\Core\Contract\ContractGen::contract();
    }

    private static function endpoint(string $source, string $package, string $resource): string
    {
        if (preg_match("/'\\/api\\/v1\\/[^']+'/", $source, $matches) === 1) {
            return trim($matches[0], "'");
        }

        if ($package === 'runapi-ai/suno') {
            $endpointName = self::match($source, '/new self\(\s*\$http,\s*\'([^\']+)\'/s');

            return '/api/v1/suno/' . $endpointName;
        }

        throw new \RuntimeException('Unable to infer endpoint for ' . $package . '::' . $resource);
    }

    private static function action(string $source, string $package, string $endpoint): string
    {
        if (preg_match("/'([a-z0-9.\\-]+\\/[a-z0-9\\-]+)'/", $source, $matches) === 1) {
            return $matches[1];
        }

        if ($package === 'runapi-ai/suno') {
            $actionName = self::match($source, '/new self\(\s*\$http,\s*\'[^\']+\',\s*\'([^\']+)\'/s');

            return 'suno/' . $actionName;
        }

        $parts = explode('/', trim($endpoint, '/'));
        $provider = str_replace('_', '-', $parts[2] ?? '');
        $resource = str_replace('_', '-', $parts[3] ?? '');

        return $provider . '/' . $resource;
    }

    private static function outputKind(string $source, string $package): string
    {
        if (str_contains($source, 'CompletedSeparateAudioStemsResponse')) {
            return 'separated_audio';
        }

        if ($package === 'runapi-ai/suno') {
            return 'audio';
        }

        return match (true) {
            str_contains($source, 'CompletedSpeechToTextResponse') => 'text',
            str_contains($source, 'CompletedAudioTaskResponse'), str_contains($source, 'TextToSpeechResponse') => 'audio',
            str_contains($source, 'CompletedImageTaskResponse') => 'image',
            str_contains($source, 'CompletedMaskTaskResponse') => 'mask',
            str_contains($source, 'CompletedSubjectStatusTaskResponse') => 'subject_status',
            str_contains($source, 'CompletedVideoTaskResponse') => 'video',
            default => 'none',
        };
    }

    /**
     * @param array<string, mixed> $contract
     *
     * @return array<string, mixed>
     */
    private static function paramsFor(string $action, array $contract, string $source, string $package, string $resource): array
    {
        $actionContract = $contract[$action] ?? null;
        $models = is_array($actionContract) ? ($actionContract['models'] ?? []) : [];
        $model = is_array($models) && $models !== [] ? $models[0] : '_';
        $fields = [];

        if (is_array($actionContract)) {
            $fieldsByModel = $actionContract['fields_by_model'] ?? [];
            if (is_array($fieldsByModel)) {
                $fields = $fieldsByModel[$model] ?? $fieldsByModel['_'] ?? [];
            }
        }

        $params = [];
        if ($model !== '_') {
            $params['model'] = $model;
        }

        if (is_array($fields)) {
            foreach ($fields as $name => $schema) {
                if ($name === 'rules' || !is_array($schema)) {
                    continue;
                }

                if ($name === 'model') {
                    $params['model'] = $model;
                    continue;
                }

                if (($schema['required'] ?? false) === true || $name === 'prompt') {
                    $params[$name] = self::sampleValue($name, $schema);
                }
            }
        }

        foreach (self::phpDocRequiredFields($source) as $name) {
            $schema = is_array($fields) && isset($fields[$name]) && is_array($fields[$name]) ? $fields[$name] : [];
            $params[$name] ??= self::sampleValue($name, $schema);
        }

        foreach (self::inlineRequiredFields($source) as $name) {
            $params[$name] ??= self::sampleValue($name, []);
        }

        if (is_array($actionContract)) {
            foreach (self::conditionalRequiredRuleFields($actionContract['rules'] ?? [], $params) as $name) {
                $schema = is_array($fields) && isset($fields[$name]) && is_array($fields[$name]) ? $fields[$name] : [];
                $params[$name] ??= self::sampleValue($name, $schema);
            }
        }

        return self::withSpecialParams($package, $resource, $params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return list<string>
     */
    private static function conditionalRequiredRuleFields(mixed $rules, array $params): array
    {
        if (!is_array($rules)) {
            return [];
        }

        $fields = [];
        foreach ($rules as $rule) {
            if (!is_array($rule)) {
                continue;
            }

            $when = $rule['when'] ?? [];
            if (!is_array($when) || $when === []) {
                continue;
            }

            $matches = true;
            foreach ($when as $name => $value) {
                if (!array_key_exists($name, $params) || $params[$name] !== $value) {
                    $matches = false;
                    break;
                }
            }

            if (!$matches) {
                continue;
            }

            $required = $rule['required'] ?? [];
            if (!is_array($required)) {
                continue;
            }

            foreach ($required as $name) {
                $fields[] = (string) $name;
            }
        }

        return array_values(array_unique($fields));
    }

    /**
     * @return list<string>
     */
    private static function phpDocRequiredFields(string $source): array
    {
        preg_match_all('/^\s*\*\s+([a-z][a-z0-9_]*):\s/m', $source, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * @return list<string>
     */
    private static function inlineRequiredFields(string $source): array
    {
        preg_match_all('/requireField\(\$params, \'([^\']+)\'\)/', $source, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private static function withSpecialParams(string $package, string $resource, array $params): array
    {
        if ($package === 'runapi-ai/suno') {
            $params['prompt'] ??= 'A warm synth-pop chorus about release day';
            $params['vocal_mode'] ??= 'auto_lyrics';
            $params['upload_url'] ??= self::AUDIO_URL;
            $params['upload_url_list'] ??= [self::AUDIO_URL, self::AUDIO_URL];
            $params['task_id'] ??= 'task_123';
            $params['audio_id'] ??= 'audio_123';
            $params['verify_url'] ??= self::AUDIO_URL;
            $params['voice_url'] ??= self::AUDIO_URL;
            $params['vocal_start_seconds'] ??= 0;
            $params['vocal_end_seconds'] ??= 5;
            $params['name'] ??= 'Release Day';
            $params['description'] ??= 'A friendly voice with clear pacing.';
        }

        if ($package === 'runapi-ai/suno' && $resource === 'replaceSection') {
            $params['upload_url'] = self::AUDIO_URL;
            $params['model'] = 'suno-v5.5';
            $params['infill_start_time'] = 10.0;
            $params['infill_end_time'] = 20.0;
            unset($params['task_id'], $params['audio_id']);
        }

        if ($package === 'runapi-ai/elevenlabs' && $resource === 'textToSpeech') {
            $params['voice'] ??= 'Narrator';
        }

        if ($package === 'runapi-ai/gemini-omni' && $resource === 'createAudio') {
            $params['model'] = 'gemini-omni-audio';
            $params['name'] ??= 'Narrator';
        }

        if ($package === 'runapi-ai/gemini-omni' && $resource === 'createCharacter') {
            $params['model'] = 'gemini-omni-character';
            $params['reference_image_url'] ??= self::PORTRAIT_URL;
            $params['character_name'] ??= 'Guide';
        }

        if ($package === 'runapi-ai/gemini-tts' && $resource === 'textToSpeech') {
            $params['speakers'] = [[
                'speaker_id' => 'Speaker 1',
                'voice_name' => 'Fenrir',
                'accent' => 'British (RP)',
                'style' => 'Deadpan',
                'pace' => 'Natural',
            ]];
            $params['dialogue_turns'] = [[
                'speaker_id' => 'Speaker 1',
                'text' => 'Welcome.',
            ]];
        }

        if ($package === 'runapi-ai/runway' && $resource === 'extendVideo') {
            $params['source_task_id'] ??= 'task_123';
            $params['output_resolution'] ??= '720p';
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $schema
     */
    private static function sampleValue(string $name, array $schema): mixed
    {
        if (isset($schema['enum']) && is_array($schema['enum']) && $schema['enum'] !== []) {
            return $schema['enum'][0];
        }

        $type = $schema['type'] ?? null;
        if ($type === 'integer') {
            return (int) ($schema['min'] ?? 1);
        }

        if ($type === 'number') {
            return (float) ($schema['min'] ?? 1.0);
        }

        if ($type === 'boolean') {
            return true;
        }

        if ($type === 'array' || str_ends_with($name, '_urls') || str_ends_with($name, '_ids') || str_ends_with($name, '_list')) {
            $itemCount = max(1, (int) ($schema['min_items'] ?? 1));

            return array_fill(0, $itemCount, self::sampleScalarForField($name));
        }

        return self::sampleScalarForField($name);
    }

    private static function sampleScalarForField(string $name): mixed
    {
        if (str_contains($name, 'audio')) {
            return self::AUDIO_URL;
        }

        if (str_contains($name, 'video')) {
            return self::VIDEO_URL;
        }

        if (str_contains($name, 'image') || str_contains($name, 'mask') || str_contains($name, 'frame') || str_contains($name, 'portrait')) {
            return str_contains($name, 'first_frame') ? self::FIRST_FRAME_URL : self::IMAGE_URL;
        }

        return match ($name) {
            'prompt' => 'A compact product render on a clean studio background',
            'text' => 'A concise narration for the generated media.',
            'lyrics', 'full_lyrics' => 'Verse one\nChorus line',
            'style', 'tags', 'negative_tags' => 'indie pop',
            'title', 'name', 'voice_name', 'character_name' => 'Release Day',
            'description', 'descriptions', 'voice_description', 'example_dialogue' => 'A friendly voice with clear pacing.',
            'task_id', 'source_task_id' => 'task_123',
            'audio_id' => 'audio_123',
            'language_code', 'language' => 'en',
            'domain_name' => 'runapi.ai',
            default => 'sample',
        };
    }

    private static function match(string $source, string $pattern): string
    {
        if (preg_match($pattern, $source, $matches) !== 1) {
            throw new \RuntimeException('Unable to match pattern ' . $pattern);
        }

        return $matches[1];
    }
}

final class UniversalSmokeClient extends BaseClient
{
}

/**
 * @phpstan-type ResourceType 'async'|'sync'
 */
final readonly class ResourceCase
{
    /**
     * @param class-string<BaseClient> $clientClass
     * @param class-string $resourceClass
     * @param 'async'|'sync' $type
     * @param array<string, mixed> $params
     */
    public function __construct(
        public string $package,
        public string $clientClass,
        public string $resourceClass,
        public string $resource,
        public string $type,
        public string $endpoint,
        public string $action,
        public string $outputKind,
        public array $params,
    ) {
    }
}
