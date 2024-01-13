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

const FUNCTIONS = [
    'WebFu\\Internal\\camelcase_to_underscore' => 'Internal/camelcase_to_underscore.php',
    'WebFu\\Internal\\get_type'                => 'Internal/get_type.php',
];

foreach (FUNCTIONS as $function => $file) {
    if (function_exists($function)) {
        continue;
    }

    require_once __DIR__.'/'.$file;
}
