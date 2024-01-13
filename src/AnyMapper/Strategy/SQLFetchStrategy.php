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

namespace WebFu\AnyMapper\Strategy;

use DateTime;

class SQLFetchStrategy extends AllowedCastingStrategy
{
    protected array $allowedDataCasting = [
        'string' => [
            'bool',
            'int',
            'float',
            DateTime::class,
        ],
    ];
}
