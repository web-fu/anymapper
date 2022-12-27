<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;
use DateTime;

class Caster
{
    private mixed $value;

    public function __construct(
        private readonly string $sourceType
    ) {
    }

    public function cast(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function as(string $destType): mixed
    {
        $stringValue =  match ($this->sourceType) {
            'int', 'float', 'bool' => (string) $this->value,
            'DateTime' => $this->value->format('Y-m-d\TH:i:s.u\Z'),
            'string' => $this->value,
        };

        return match ($destType) {
            'int' => (int) $stringValue,
            'float' => (float) $stringValue,
            'bool' => (bool) $stringValue,
            'DateTime' => new DateTime($stringValue),
            'string' => $stringValue,
        };
    }
}
