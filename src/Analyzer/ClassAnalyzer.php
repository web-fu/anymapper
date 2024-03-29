<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use WebFu\Reflection\ReflectionClass;
use WebFu\Reflection\ReflectionMethod;
use WebFu\Reflection\ReflectionParameter;
use WebFu\Reflection\ReflectionProperty;

use WebFu\Reflection\ReflectionTypeExtended;

use function WebFu\Internal\camelcase_to_underscore;

class ClassAnalyzer implements AnalyzerInterface
{
    /** @var ReflectionProperty[] */
    private array $properties = [];
    private ReflectionMethod|null $constructor = null;
    /** @var ReflectionMethod[] */
    private array $generators = [];
    /** @var ReflectionMethod[] */
    private array $getters = [];
    /** @var ReflectionMethod[] */
    private array $setters = [];
    /** @var Track[] */
    private array $inputTrackList = [];
    /** @var Track[] */
    private array $outputTrackList = [];

    /**
     * @param object|class-string $class
     */
    public function __construct(object|string $class)
    {
        $reflection = new ReflectionClass($class);

        $this->init($reflection);
    }

    private function init(ReflectionClass $reflection): void
    {
        if ($parent = $reflection->getParentClass()) {
            $this->init($parent);
        }

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
            $underscoreName = camelcase_to_underscore($property->getName());
            $types = $property->getTypeExtended();

            if (!$property->isReadOnly()) {
                $this->inputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
            }

            $this->outputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            //Standard Getters and Setters
            if (preg_match('#^get[A-Z]+|is[A-Z]+#', $method->getName())) {
                if ($method->getNumberOfRequiredParameters() > 0) {
                    continue;
                }

                $this->getters[$method->getName()] = $method;

                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^get_|is_#', '', $underscoreName);
                $this->outputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $method->getReturnTypeExtended());
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
                $parameters = $method->getParameters();

                /** @var ReflectionParameter $firstParameter */
                $firstParameter = array_shift($parameters);
                $this->inputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $firstParameter->getTypeExtended());
            }

            //Magic Method Getters and Setters
            if ($method->getName() === '__get') {
                $this->getters[$method->getName()] = $method;
                $this->outputTrackList['__get'] = new Track($method->getName(), TrackType::METHOD, new ReflectionTypeExtended(['mixed']));
            }

            if ($method->getName() === '__set') {
                $this->setters[$method->getName()] = $method;
                $this->inputTrackList['__set'] = new Track($method->getName(), TrackType::METHOD, new ReflectionTypeExtended(['mixed']));
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

    /** @return Track[] */
    public function getOutputTrackList(): array
    {
        return $this->outputTrackList;
    }

    /** @return Track[] */
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
}
