<?php

declare(strict_types=1);

namespace WebFu\Internal;

use RuntimeException;

/**
 * @internal
 */
function camelcase_to_underscore(string $string): string
{
    if (empty($string)) {
        return $string;
    }
    $str = lcfirst($string);
    $str = preg_replace('/[A-Z]/', '_$0', $str);

    assert(is_string($str));

    return strtolower($str);
}
