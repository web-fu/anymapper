<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

use WebFu\Reflection\ExtendedReflectionClass;
use WebFu\Reflection\ExtendedReflectionProperty;

use function WebFu\Internal\camelcase_to_underscore;
use function WebFu\Internal\reflection_type_names;

class ClassAnalyzer implements AnalyzerInterface
{
    /** @var ExtendedReflectionProperty[] */
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
        $reflection = new ExtendedReflectionClass($class);

        $this->init($reflection);
    }

    /**
     * @param ExtendedReflectionClass<object> $reflection
     */
    private function init(ExtendedReflectionClass $reflection): void
    {
        if ($parent = $reflection->getExtendedParentClass()) {
            $this->init($parent);
        }

        foreach ($reflection->getExtendedProperties(ExtendedReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
            $underscoreName = camelcase_to_underscore($property->getName());
            $types = $property->getTypes();

            if (!$property->isReadOnly()) {
                $this->inputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
            }

            $this->outputTrackList[$underscoreName] = new Track($property->getName(), TrackType::PROPERTY, $types);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                '__get' === $method->getName()
                || preg_match('#^get[A-Z]+|is[A-Z]+#', $method->getName())
            ) {
                $this->getters[$method->getName()] = $method;

                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^get_|is_#', '', $underscoreName);
                $types = reflection_type_names($method->getReturnType());
                $this->outputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $types);
            }
            if (
                '__set' === $method->getName()
                || preg_match('#^set[A-Z]+#', $method->getName())
            ) {
                $this->setters[$method->getName()] = $method;
                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^set_#', '', $underscoreName);
                $parameters = $method->getParameters();
                if (!count($parameters)) {
                    continue;
                }
                $lastParameter = array_pop($parameters);
                $types = reflection_type_names($lastParameter->getType());
                $this->inputTrackList[$underscoreName] = new Track($method->getName(), TrackType::METHOD, $types);
            }
            /** @var ReflectionNamedType|ReflectionUnionType|null $returnType */
            $returnType = $method->getReturnType();
            $returnTypeNames = reflection_type_names($returnType);

            if (
                !empty(array_intersect($returnTypeNames, [
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
     * @return ExtendedReflectionProperty[]
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
