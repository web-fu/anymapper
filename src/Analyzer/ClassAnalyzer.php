<?php

declare(strict_types=1);

namespace WebFu\Analyzer;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use function WebFu\Mapper\camelcase_to_underscore;

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
            /** @var \ReflectionNamedType|\ReflectionUnionType|null $returnType */
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

    /** @return string[] */
    public function getGettablePathMap(): array
    {
        $gettableNames = $this->getGettableNames();

        return $this->aliasList($gettableNames);
    }

    /** @return string[] */
    public function getSettablePathMap(): array
    {
        $settableNames = $this->getSettableNames();

        return $this->aliasList($settableNames);
    }

    /** @return string[] */
    private function aliasList(array $names): array
    {
        $aliasList = [];
        foreach ($names as $name) {
            $underscoreName = camelcase_to_underscore($name);
            $aliasList[$underscoreName] = $name;

            preg_match('#^(get|is|set)(?P<name>[A-Z][\w]+)#', $name, $matches);

            if (isset($matches['name'])) {
                $underscoreName = camelcase_to_underscore($matches['name']);
                $aliasList[$underscoreName] = $name;
            }
        }

        return $aliasList;
    }

    /**
     * @return string[]
     */
    public function getGettableNames(): array
    {
        $propertyNames = array_keys($this->getProperties());
        $functionNames = array_keys($this->getGetters());

        return array_merge($propertyNames, $functionNames);
    }

    public function getGettableMethod(string $path): ReflectionMethod|null
    {
        foreach ($this->getGetters() as $name => $method) {
            if ($name === $path) {
                return $method;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getSettableNames(): array
    {
        $propertyNames = array_keys($this->getProperties());
        $functionNames = array_keys($this->getSetters());

        return array_merge($propertyNames, $functionNames);
    }

    public function getSettableMethod(string $path): ReflectionMethod|null
    {
        foreach ($this->getSetters() as $name => $method) {
            if ($name === $path) {
                return $method;
            }
        }

        return null;
    }
}
