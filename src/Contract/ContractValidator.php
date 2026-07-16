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
        $models = $actionContract['models'];
        if ($models !== []) {
            if ($model === '_') {
                throw new ValidationException('model is required');
            }
            if (!in_array($model, $models, true)) {
                throw new ValidationException('model must be one of the allowed values');
            }
        }

        $fields = $fieldsByModel[$model] ?? $fieldsByModel['_'] ?? null;
        if ($fields === null) {
            return;
        }

        $this->validateRules($actionContract['rules'] ?? [], $model, $params);

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
        $hasValue = $this->fieldPresent($name, $params);
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

        if (($schema['type'] ?? null) === 'integer') {
            $this->validateInteger($name, $schema, $value);
        }

        $this->validateRange($name, $schema, $value);
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function validateInteger(string $name, array $schema, mixed $value): void
    {
        if (is_int($value)) {
            return;
        }

        $detail = array_key_exists('min', $schema) && array_key_exists('max', $schema)
            ? ' between ' . $schema['min'] . ' and ' . $schema['max']
            : '';

        throw new ValidationException($name . ' must be an integer' . $detail);
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function validateRange(string $name, array $schema, mixed $value): void
    {
        if (!array_key_exists('min', $schema) && !array_key_exists('max', $schema)) {
            return;
        }

        if (($schema['length'] ?? false) === true) {
            $length = mb_strlen((string) $value);
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

    /**
     * @param list<array{when?: array<string, mixed>, required?: list<string>, forbidden?: list<string>}> $rules
     * @param array<string, mixed> $params
     */
    private function validateRules(array $rules, string $model, array $params): void
    {
        foreach ($rules as $rule) {
            $conditions = $rule['when'] ?? [];
            if (!$this->conditionsMet($conditions, $model, $params)) {
                continue;
            }

            $context = $this->conditionDescription($conditions);
            foreach (($rule['required'] ?? []) as $field) {
                $field = (string) $field;
                if (!$this->fieldPresent($field, $params)) {
                    throw new ValidationException($field . ' is required when ' . $context);
                }
            }

            foreach (($rule['forbidden'] ?? []) as $field) {
                $field = (string) $field;
                if ($this->fieldPresent($field, $params)) {
                    throw new ValidationException($field . ' is not allowed when ' . $context);
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $conditions
     * @param array<string, mixed> $params
     */
    private function conditionsMet(array $conditions, string $model, array $params): bool
    {
        foreach ($conditions as $field => $expected) {
            if ($field === 'model') {
                if ($model !== (string) $expected) {
                    return false;
                }

                continue;
            }

            if (!array_key_exists($field, $params) || (string) $params[$field] !== (string) $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $conditions
     */
    private function conditionDescription(array $conditions): string
    {
        ksort($conditions);
        $parts = [];
        foreach ($conditions as $field => $value) {
            $parts[] = $field . ' is ' . $value;
        }

        return implode(' and ', $parts);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function fieldPresent(string $field, array $params): bool
    {
        if (!array_key_exists($field, $params)) {
            return false;
        }

        $value = $params[$field];
        if ($value === false) {
            return true;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->presentValue($item)) {
                    return true;
                }
            }

            return false;
        }

        return $this->presentValue($value);
    }

    private function presentValue(mixed $value): bool
    {
        if ($value === null || $value === false) {
            return false;
        }

        if ($value === true) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return $value !== [];
        }

        return true;
    }
}
