<?php

declare(strict_types=1);

namespace WebFu\AnyMapper\Caster;

interface CasterInterface
{
    public function cast(mixed $value, string $type): mixed;
}
