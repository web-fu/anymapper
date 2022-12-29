<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use DateTime;
use WebFu\Tests\Unit\AnyMapper\CasterException;

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
        private readonly int|float|bool|string|object|array $value
    ) {
    }

    public function as(string $destType): int|float|bool|string|object|array
    {
        $sourceType = gettype($this->value);

        if ($sourceType === $destType) {
            return $this->value;
        }

        if (!in_array($destType, self::ALLOWED[$sourceType])) {
            throw new CasterException('Data casting from '.$sourceType.' to '.$destType.' not allowed');
        }

        $this->destType = $destType;

        return match ($sourceType) {
            'integer', 'double', 'boolean', 'string', 'NULL' => $this->scalarConversion(),
            'object', 'array' => $this->complexConversion(),
            default => throw new CasterException('Data casting from '.$sourceType.' not allowed'),
        };
    }

    private function scalarConversion(): int|float|bool|string|DateTime
    {
        return match ($this->destType) {
            'int', 'integer' => (int) $this->value,
            'double', 'float' => (float) $this->value,
            'bool', 'boolean' => (bool) $this->value,
            'string' => (string) $this->value,
            'DateTime' => new DateTime($this->value),
            default => throw new CasterException('Unknown error'),
        };
    }

    private function complexConversion(): object|array|string
    {
        return match ($this->destType) {
            'string' => var_export($this->value, true),
            'object' => (object) $this->value,
            'array' => (array) $this->value,
            'DateTime' => new DateTime($this->value),
            default => throw new CasterException('Unknown error'),
        };
    }
}
