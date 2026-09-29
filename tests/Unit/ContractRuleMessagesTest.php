<?php

declare(strict_types=1);

namespace RunApi\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RunApi\Core\Contract\ContractRepository;
use RunApi\Core\Contract\ContractValidator;
use RunApi\Core\Errors\ValidationException;

/**
 * Rule messages must match every other SDK (sdk/contract_rule_messages.json).
 */
final class ContractRuleMessagesTest extends TestCase
{
    private const SDK_ROOT = __DIR__ . '/../../../../..';

    /**
     * @return iterable<string, array{string, array<string, mixed>, string}>
     */
    public static function cases(): iterable
    {
        $fixture = self::SDK_ROOT . '/contract_rule_messages.json';
        if (!is_file($fixture)) {
            // The shared fixture lives in the SDK monorepo; the public package repo does not ship it.
            // PHPUnit rejects an empty data provider, so hand the test one case to skip.
            yield 'shared SDK fixture not available' => ['', [], ''];

            return;
        }

        $shared = json_decode((string) file_get_contents($fixture), true, flags: JSON_THROW_ON_ERROR);
        foreach ($shared['cases'] as $case) {
            yield $case['name'] => [$case['action'], $case['params'], $case['message']];
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('cases')]
    public function testRuleMessageMatchesSharedFixture(string $action, array $params, string $message): void
    {
        if ($action === '') {
            self::markTestSkipped('shared SDK fixture not available');
        }

        $contract = json_decode((string) file_get_contents(self::SDK_ROOT . '/contract.json'), true, flags: JSON_THROW_ON_ERROR);
        $validator = new ContractValidator(new ContractRepository($contract['actions']));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($message);

        $validator->validate($action, (string) $params['model'], $params);
    }
}
