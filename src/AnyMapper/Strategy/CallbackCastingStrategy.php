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

use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

use WebFu\Reflection\ReflectionType;

class CallbackCastingStrategy extends StrictStrategy
{
    /**
     * @var array<string, array<string, callable>>
     */
    protected array $methods = [];

    public function addMethod(string $from, string $to, callable $callback): self
    {
        $this->methods[$from][$to] = $callback;

        return $this;
    }

    public function cast(mixed $value, ReflectionType $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType   = get_type($value);

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        foreach ($this->methods[$sourceType] ?? [] as $to => $callback) {
            if (!in_array($to, $allowedTypes, true)) {
                continue;
            }

            return $callback($value);
        }

        throw new MapperException('Cannot convert type '.$sourceType.' into any of the following types: '.implode(',', $allowedTypes));
    }
}
