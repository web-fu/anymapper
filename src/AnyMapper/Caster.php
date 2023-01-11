<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use DateTime;

class Caster
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

    private string $destType;

    public function __construct(
        private mixed $value
    ) {
    }

    public function as(string $destType): mixed
    {
        $sourceType = gettype($this->value);

        if ($sourceType === $destType) {
            return $this->value;
        }

        if (!in_array($destType, self::ALLOWED[$sourceType])) {
            throw new CasterException('Data casting from '.$sourceType.' to '.$destType.' not allowed');
        }

        $this->destType = $destType;

        if (is_null($this->value)) {
            return null;
        }

        if (is_scalar($this->value)) {
            return $this->scalarConversion();
        }

        if (is_array($this->value) or is_object($this->value)) {
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
     * @return object|mixed[]|string
     */
    private function complexConversion(): object|array|string
    {
        assert(is_array($this->value) or is_object($this->value));
        return match ($this->destType) {
            'string' => var_export($this->value, true),
            'object' => (object) $this->value,
            'array' => (array) $this->value,
            default => throw new CasterException('Unknown error'),
        };
    }
}
