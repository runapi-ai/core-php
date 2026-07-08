<?php

declare(strict_types=1);

namespace RunApi\Core\Contract;

use RunApi\Core\Errors\ValidationException;

/**
 * Validates request parameters against generated RunAPI contract metadata.
 */
final readonly class ContractValidator
{
    /**
     * Create a contract validator instance.
     */
    public function __construct(private ContractRepository $repository = new ContractRepository())
    {
    }

    /**
     * Validate request parameters for an action and model.
     *
     * @param array<string, mixed> $params
     */
    public function validate(string $action, string $model, array $params): void
    {
        $actionContract = $this->repository->action($action);
        if ($actionContract === null) {
            return;
        }

        $fieldsByModel = $actionContract['fields_by_model'];
        $fields = $fieldsByModel[$model] ?? null;
        if ($fields === null) {
            return;
        }

        foreach ($fields as $name => $schema) {
            $this->validateField($name, $schema, $params);
        }
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<string, mixed> $params
     */
    private function validateField(string $name, array $schema, array $params): void
    {
        $hasValue = array_key_exists($name, $params) && $params[$name] !== null;
        if (($schema['required'] ?? false) === true && !$hasValue) {
            throw new ValidationException($name . ' is required');
        }

        if (!$hasValue) {
            return;
        }

        $value = $params[$name];
        if (array_key_exists('enum', $schema)) {
            $enum = $schema['enum'];
            if (!is_array($enum) || !in_array($value, $enum, true)) {
                throw new ValidationException($name . ' must be one of the allowed values');
            }
        }

        $this->validateRange($name, $schema, $value);
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function validateRange(string $name, array $schema, mixed $value): void
    {
        if (!array_key_exists('min', $schema) && !array_key_exists('max', $schema)) {
            return;
        }

        if (is_string($value)) {
            $length = mb_strlen($value);
            if (array_key_exists('min', $schema) && $length < (int) $schema['min']) {
                throw new ValidationException($name . ' must be at least ' . $schema['min'] . ' characters');
            }

            if (array_key_exists('max', $schema) && $length > (int) $schema['max']) {
                throw new ValidationException($name . ' must be at most ' . $schema['max'] . ' characters');
            }

            return;
        }

        if (!is_int($value) && !is_float($value)) {
            throw new ValidationException($name . ' must be numeric');
        }

        $number = (float) $value;
        if (array_key_exists('min', $schema) && $number < (float) $schema['min']) {
            throw new ValidationException($name . ' must be greater than or equal to ' . $schema['min']);
        }

        if (array_key_exists('max', $schema) && $number > (float) $schema['max']) {
            throw new ValidationException($name . ' must be less than or equal to ' . $schema['max']);
        }
    }
}
