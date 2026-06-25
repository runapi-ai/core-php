<?php

declare(strict_types=1);

namespace RunApi\Core\Contract;

/**
 * Read-only access to generated request contract metadata.
 */
final class ContractRepository
{
    /**
     * Create a contract repository instance.
     *
     * @param array<string, array{models: list<string>, fields_by_model: array<string, array<string, array<string, mixed>>>}>|null $contract
     */
    public function __construct(private ?array $contract = null)
    {
    }

    /**
     * Return all generated contract metadata.
     *
     * @return array<string, array{models: list<string>, fields_by_model: array<string, array<string, array<string, mixed>>>}>
     */
    public function all(): array
    {
        return $this->contract ?? ContractGen::contract();
    }

    /**
     * Return contract metadata for one action, or null when unknown.
     *
     * @return array{models: list<string>, fields_by_model: array<string, array<string, array<string, mixed>>>}|null
     */
    public function action(string $action): ?array
    {
        $contract = $this->all();

        return $contract[$action] ?? null;
    }
}
