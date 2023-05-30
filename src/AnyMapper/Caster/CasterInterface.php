<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Caster;

interface CasterInterface
{
    public function setValue(mixed $value): self;
    public function as(string $type): mixed;
}