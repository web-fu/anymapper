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

function get_type(mixed $value): string
{
    $type = get_debug_type($value);
    if (str_starts_with($type, 'resource')) {
        $type = 'resource';
    }

    return $type;
}
