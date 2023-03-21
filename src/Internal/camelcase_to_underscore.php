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

    if (
        preg_last_error()
        || null === $str
    ) {
        throw new RuntimeException('Regular exception error: '.preg_last_error_msg());
    }

    return strtolower($str);
}
