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

use WebFu\Analyzer\ClassAnalyzer;
use WebFu\AnyMapper\MapperException;

use function WebFu\Internal\get_type;

use WebFu\Reflection\ReflectionType;

class DocBlockDetectStrategy extends StrictStrategy
{
    public function cast(mixed $value, ReflectionType $allowed): mixed
    {
        $allowedTypes = array_merge($allowed->getTypeNames(), $allowed->getPhpDocTypeNames());
        $sourceType   = get_type($value);

        // Remove mixed type
        $allowedTypes = array_filter(
            $allowedTypes,
            fn (string $type) => 'mixed' !== $type
        );

        if ($this->noCastingNeeded($sourceType, $allowedTypes)) {
            return $value;
        }

        // Autodetect can be used only on classes
        $classDestinationTypes = array_filter(
            $allowedTypes,
            fn (string $type) => class_exists($type)
        );

        foreach ($classDestinationTypes as $class) {
            $analyzer = new ClassAnalyzer($class);

            /* Constructor does not accept parameters */
            if (!$analyzer->getConstructor()?->getNumberOfParameters()) {
                continue;
            }

            /* Constructor require more than one parameter */
            if ($analyzer->getConstructor()->getNumberOfRequiredParameters() > 1) {
                continue;
            }

            $constructorParameters = $analyzer->getConstructor()->getParameters();

            $allowedTypes = $constructorParameters[0]->getType()->getTypeNames();

            foreach ($allowedTypes as $allowedType) {
                if ($sourceType !== $allowedType) {
                    continue;
                }

                return new $class($value);
            }
        }

        throw new MapperException('Cannot convert type '.$sourceType.' into any of the following types: '.implode(',', $allowedTypes));
    }
}
