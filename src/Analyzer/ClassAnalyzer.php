<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use function WebFu\Internal\camelcase_to_underscore;
use function WebFu\Internal\reflection_type_names;
use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

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

    public function __construct(object $class)
    {
        $reflection = new ReflectionClass($class);

        $this->init($reflection);
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    private function init(ReflectionClass $reflection): void
    {
        if ($parent = $reflection->getParentClass()) {
            $this->init($parent);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ('__get' === $method->getName()
                or preg_match('#^get[A-Z]+|is[A-Z]+#', $method->getName())
            ) {
                $this->getters[$method->getName()] = $method;
            }
            if ('__set' === $method->getName()
                or preg_match('#^set[A-Z]+#', $method->getName())
            ) {
                $this->setters[$method->getName()] = $method;
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
                and $method->isStatic()
            ) {
                $this->generators[$method->getName()] = $method;
            }
        }

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
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

    /** @return ElementAnalyzer[] */
    public function getOutputTrackList(): array
    {
        $trackList = [];
        foreach (array_keys($this->getProperties()) as $propertyName) {
            $underscoreName = camelcase_to_underscore($propertyName);
            $trackList[$underscoreName] = new ElementAnalyzer($propertyName, ElementType::PROPERTY);
        }
        foreach (array_keys($this->getGetters()) as $getterName) {
            $underscoreName = camelcase_to_underscore($getterName);
            $trackList[$underscoreName] = new ElementAnalyzer($getterName, ElementType::METHOD);
        }

        return $trackList;
    }

    /** @return ElementAnalyzer[] */
    public function getInputTrackList(): array
    {
        $trackList = [];
        foreach (array_keys($this->getProperties()) as $propertyName) {
            $underscoreName = camelcase_to_underscore($propertyName);
            $trackList[$underscoreName] = new ElementAnalyzer($propertyName, ElementType::PROPERTY);
        }
        foreach (array_keys($this->getSetters()) as $setterName) {
            $underscoreName = camelcase_to_underscore($setterName);
            $trackList[$underscoreName] = new ElementAnalyzer($setterName, ElementType::METHOD);
        }

        return $trackList;
    }
}
