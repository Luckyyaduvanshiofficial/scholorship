<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Create a new validator instance.
     */
    public static function make(array $data): self
    {
        return new self($data);
    }

    /**
     * Field is required (not empty string, not null).
     */
    public function required(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? null;

        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->addError($field, $label ?: $field, 'is required.');
        }

        return $this;
    }

    /**
     * Field must be a valid email address.
     */
    public function email(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $label ?: $field, 'must be a valid email address.');
        }

        return $this;
    }

    /**
     * Field must be a valid mobile number (10 digits, Indian format).
     */
    public function mobile(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !preg_match('/^[6-9]\d{9}$/', $value)) {
            $this->addError($field, $label ?: $field, 'must be a valid 10-digit mobile number.');
        }

        return $this;
    }

    /**
     * Minimum length for a string field.
     */
    public function min(string $field, int $min, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if (is_string($value) && mb_strlen(trim($value)) < $min) {
            $this->addError($field, $label ?: $field, "must be at least {$min} characters.");
        }

        return $this;
    }

    /**
     * Maximum length for a string field.
     */
    public function max(string $field, int $max, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if (is_string($value) && mb_strlen(trim($value)) > $max) {
            $this->addError($field, $label ?: $field, "must not exceed {$max} characters.");
        }

        return $this;
    }

    /**
     * Field must be numeric.
     */
    public function numeric(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !is_numeric($value)) {
            $this->addError($field, $label ?: $field, 'must be a number.');
        }

        return $this;
    }

    /**
     * Field value must be in the whitelist.
     */
    public function in(string $field, array $allowed, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->addError($field, $label ?: $field, 'contains an invalid value.');
        }

        return $this;
    }

    /**
     * Field must match another field (e.g., password confirmation).
     */
    public function matches(string $field, string $matchField, string $label = ''): self
    {
        if (($this->data[$field] ?? '') !== ($this->data[$matchField] ?? '')) {
            $this->addError($field, $label ?: $field, "must match {$matchField}.");
        }

        return $this;
    }

    /**
     * Field must be a valid date.
     */
    public function date(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !strtotime($value)) {
            $this->addError($field, $label ?: $field, 'must be a valid date.');
        }

        return $this;
    }

    /**
     * Check if validation passed (no errors).
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error for a field.
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    private function addError(string $field, string $label, string $message): void
    {
        $this->errors[$field][] = ucfirst($label) . ' ' . $message;
    }
}
