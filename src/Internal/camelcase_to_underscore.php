<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\Internal;

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
