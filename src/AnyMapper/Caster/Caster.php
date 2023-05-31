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

    public function cast(mixed $value, string $type): mixed
    {
        $sourceType = gettype($value);

        if ($sourceType === $type) {
            return $value;
        }

        if (
            str_ends_with($type, '[]')
            && is_iterable($value)
        ) {
            $type = str_replace('[]', '', $type);
            $result = [];
            foreach ($value as $item) {
                $result[] = (new self())->cast($item, $type);
            }
            return $result;
        }

        if (!in_array($type, self::ALLOWED[$sourceType])) {
            throw new CasterException('Data casting from '.$sourceType.' to '.$type.' not allowed');
        }

        if (is_null($value)) {
            return null;
        }

        if (is_scalar($value)) {
            return $this->scalarConversion($value, $type);
        }

        if (
            is_iterable($value)
            || is_object($value)
        ) {
            return $this->complexConversion($value, $type);
        }

        throw new CasterException('Data casting from '.$sourceType.' not allowed');
    }

    private function scalarConversion(int|float|bool|string $value, string $type): int|float|bool|string|DateTime
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'double', 'float' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string' => (string) $value,
            'DateTime' => new DateTime((string) $value),
            default => throw new CasterException('Unknown error'),
        };
    }

    /**
     * @param iterable<mixed>|object $value
     * @return object|iterable<mixed>|string
     */
    private function complexConversion(iterable|object $value, string $type): object|iterable|string
    {
        if ($type === 'string') {
            return var_export($value, true);
        }

        return match ($type) {
            'object' => (object) $value,
            'array' => (array) $value,
            default => throw new CasterException('Unknown error'),
        };
    }
}
