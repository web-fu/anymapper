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
    /** @var ElementAnalyzer[]  */
    private array $inputTrackList = [];
    /** @var ElementAnalyzer[]  */
    private array $outputTrackList = [];

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

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
            $underscoreName = camelcase_to_underscore($property->getName());

            if (!$property->isReadOnly()) {
                $this->inputTrackList[$underscoreName]  = new ElementAnalyzer($property->getName(), ElementType::PROPERTY);
            }

            $this->outputTrackList[$underscoreName]  = new ElementAnalyzer($property->getName(), ElementType::PROPERTY);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ('__get' === $method->getName()
                or preg_match('#^get[A-Z]+|is[A-Z]+#', $method->getName())
            ) {
                $this->getters[$method->getName()] = $method;

                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^get_|is_#', '', $underscoreName);
                $this->outputTrackList[$underscoreName] = new ElementAnalyzer($method->getName(), ElementType::METHOD);
            }
            if ('__set' === $method->getName()
                or preg_match('#^set[A-Z]+#', $method->getName())
            ) {
                $this->setters[$method->getName()] = $method;
                $underscoreName = camelcase_to_underscore($method->getName());
                $underscoreName = preg_replace('#^set_#', '', $underscoreName);
                $this->inputTrackList[$underscoreName] = new ElementAnalyzer($method->getName(), ElementType::METHOD);
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
        return $this->outputTrackList;
    }

    /** @return ElementAnalyzer[] */
    public function getInputTrackList(): array
    {
        return $this->inputTrackList;
    }
}
