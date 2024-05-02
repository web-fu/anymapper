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

use WebFu\AnyMapper\Caster\CasterInterface;
use WebFu\AnyMapper\Caster\DefaultCaster;
use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

use WebFu\Reflection\ReflectionType;

class AllowedCastingStrategy extends StrictStrategy
{
    protected CasterInterface $caster;

    /**
     * @var array<string[]>
     */
    protected array $allowedDataCasting = [];

    public function __construct(CasterInterface|null $caster = null)
    {
        $this->caster = $caster ?: new DefaultCaster();
    }

    public function allow(string $from, string $to): self
    {
        $this->allowedDataCasting[$from][] = $to;

        return $this;
    }

    public function cast(mixed $value, ReflectionType $allowed): mixed
    {
        $allowedTypes = $allowed->getTypeNames();
        $sourceType   = get_type($value);

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        $allowedDataCasting = $this->allowedDataCasting[$sourceType] ?? [];

        foreach ($allowedDataCasting as $to) {
            if (!in_array($to, $allowedTypes, true)) {
                continue;
            }

            return $this->caster->cast($value, $to);
        }

        throw new MapperException('Cannot convert type '.$sourceType.' into any of the following types: '.implode(',', $allowedTypes));
    }
}
