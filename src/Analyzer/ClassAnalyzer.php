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

namespace WebFu\Analyzer;

use function WebFu\Internal\camelcase_to_underscore;

use WebFu\Reflection\ReflectionClass;
use WebFu\Reflection\ReflectionEnum;
use WebFu\Reflection\ReflectionMethod;
use WebFu\Reflection\ReflectionParameter;
use WebFu\Reflection\ReflectionProperty;
use WebFu\Reflection\ReflectionType;
use WebFu\Reflection\WrongPhpVersionException;

class ClassAnalyzer implements AnalyzerInterface
{
    /**
     * @var ReflectionProperty[]
     */
    private array $properties                  = [];
    private ReflectionMethod|null $constructor = null;
    /**
     * @var ReflectionMethod[]
     */
    private array $generators = [];
    /**
     * @var ReflectionMethod[]
     */
    private array $getters = [];
    /**
     * @var ReflectionMethod[]
     */
    private array $setters = [];
    /**
     * @var Track[]
     */
    private array $inputTrackList = [];
    /**
     * @var Track[]
     */
    private array $outputTrackList = [];

    /**
     * @param object|class-string $originalClass
     */
    public function __construct(private object|string $originalClass)
    {
        $this->init($this->originalClass);
    }

    public function isBackedEnum(): bool
    {
        $reflectionClass = new ReflectionClass($this->originalClass);

        try {
            if (!$reflectionClass->isEnum()) {
                return false;
            }
        } catch (WrongPhpVersionException $e) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        $reflectionEnum = new ReflectionEnum($this->originalClass);

        return $reflectionEnum->isBacked();
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getConstructor(): ReflectionMethod|null
    {
        return $this->constructor;
    }

    /**
     * @return ReflectionMethod[]
     */
    public function getGenerators(): array
    {
        return $this->generators;
    }

    /**
     * @return ReflectionMethod[]
     */
    public function getGetters(): array
    {
        return $this->getters;
    }

    /**
     * @return ReflectionMethod[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * @return Track[]
     */
    public function getOutputTrackList(): array
    {
        return $this->outputTrackList;
    }

    /**
     * @return Track[]
     */
    public function getInputTrackList(): array
    {
        return $this->inputTrackList;
    }

    public function getOutputTrack(string $track): Track|null
    {
        return $this->outputTrackList[$track] ?? null;
    }

    public function getInputTrack(string $track): Track|null
    {
        return $this->inputTrackList[$track] ?? null;
    }

    /**
     * @param object|class-string $class
     */
    private function init(object|string $class): void
    {
        $reflection = new ReflectionClass($class);

        if ($parent = $reflection->getParentClass()) {
            $this->init($parent->getName());
        }

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
            $underscoreName                         = camelcase_to_underscore($property->getName());
            $types                                  = $property->getType();

            if (!$property->isReadOnly()) {
                $this->inputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
            }

            $this->outputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Standard Getters and Setters
            if (preg_match('#^get[A-Z]+|is[A-Z]+#', $method->getName())) {
                if ($method->getNumberOfRequiredParameters() > 0) {
                    continue;
                }

                $this->getters[$method->getName()] = $method;

                $underscoreName                         = camelcase_to_underscore($method->getName());
                $underscoreName                         = preg_replace('#^get_|is_#', '', $underscoreName);
                $this->outputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $method->getReturnType());
            }

            if (preg_match('#^set[A-Z]+#', $method->getName())) {
                if (!$method->getNumberOfParameters()) {
                    continue;
                }

                if ($method->getNumberOfRequiredParameters() > 1) {
                    continue;
                }

                $this->setters[$method->getName()] = $method;

                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^set_#', '', $underscoreName);
                $parameters     = $method->getParameters();

                /** @var ReflectionParameter $firstParameter */
                $firstParameter                        = array_shift($parameters);
                $this->inputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $firstParameter->getType());
            }

            // Magic Method Getters and Setters
            if ('__get' === $method->getName()) {
                $this->getters[$method->getName()] = $method;
                $this->outputTrackList['__get']    = new Track($method->getName(), TrackType::METHOD, new ReflectionType(['mixed']));
            }

            if ('__set' === $method->getName()) {
                $this->setters[$method->getName()] = $method;
                $this->inputTrackList['__set']     = new Track($method->getName(), TrackType::METHOD, new ReflectionType(['mixed']));
            }

            $returnType = $method->getReturnType();

            if (
                !empty(array_intersect($returnType->getTypeNames(), [
                    $reflection->getName(),
                    'self',
                    'static',
                ]))
                && $method->isStatic()
            ) {
                $this->generators[$method->getName()] = $method;
            }
        }
    }
}
