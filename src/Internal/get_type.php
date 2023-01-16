<?php

declare(strict_types=1);

namespace WebFu\Internal;

function get_type(mixed $value): string
{
    $type = get_debug_type($value);
    if (str_starts_with($type, 'resource')) {
        $type = 'resource';
    }
    return $type;
}