<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Caster;

use DateTime;

class Caster implements CasterInterface
{
    private const ALLOWED = [
        'boolean' => [
            'int',
            'integer',
            'string',
        ],
        'integer' => [
            'bool',
            'boolean',
            'double',
            'float',
            'string',
        ],
        'double' => [
            'string',
        ],
        'string' => [
            'bool',
            'boolean',
            'int',
            'integer',
            'double',
            'float',
            'DateTime',
        ],
        'NULL' => [
        ],
        'array' => [
            'object',
            'string',
        ],
        'object' => [
            'array',
            'string',
        ],
        'resource' => [
        ],
        'unknown type' => [
        ],
        'resource (closed)' => [
        ],
    ];

    private mixed $value;
    private string $destType;

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function as(string $type): mixed
    {
        $sourceType = gettype($this->value);

        if ($sourceType === $type) {
            return $this->value;
        }

        if (
            str_ends_with($type, '[]')
            && is_iterable($this->value)
        ) {
            $type = str_replace('[]', '', $type);
            $result = [];
            foreach ($this->value as $value) {
                $result[] = (new self())->setValue($value)->as($type);
            }
            return $result;
        }

        if (!in_array($type, self::ALLOWED[$sourceType])) {
            throw new CasterException('Data casting from '.$sourceType.' to '.$type.' not allowed');
        }

        $this->destType = $type;

        if (is_null($this->value)) {
            return null;
        }

        if (is_scalar($this->value)) {
            return $this->scalarConversion();
        }

        if (
            is_iterable($this->value)
            || is_object($this->value)
        ) {
            return $this->complexConversion();
        }

        throw new CasterException('Data casting from '.$sourceType.' not allowed');
    }

    private function scalarConversion(): int|float|bool|string|DateTime
    {
        assert(is_scalar($this->value));
        return match ($this->destType) {
            'int', 'integer' => (int) $this->value,
            'double', 'float' => (float) $this->value,
            'bool', 'boolean' => (bool) $this->value,
            'string' => (string) $this->value,
            'DateTime' => new DateTime((string) $this->value),
            default => throw new CasterException('Unknown error'),
        };
    }

    /**
     * @return object|iterable<mixed>|string
     */
    private function complexConversion(): object|iterable|string
    {
        assert(is_iterable($this->value) || is_object($this->value));

        if ($this->destType === 'string') {
            return var_export($this->value, true);
        }

        return match ($this->destType) {
            'object' => (object) $this->value,
            'array' => (array) $this->value,
            default => throw new CasterException('Unknown error'),
        };
    }
}
