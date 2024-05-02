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

use WebFu\Reflection\ReflectionType;

interface StrategyInterface
{
    public function cast(mixed $value, ReflectionType $allowed): mixed;
}
