<?php

declare(strict_types=1);

namespace WebFu\Mapper;

class ClassAnalyzer implements AnalyzerInterface
{
    private array $properties = [];
    private \ReflectionMethod|null $constructor = null;
    private array $generators = [];
    private array $getters = [];
    private array $setters = [];

    public function __construct(object $class)
    {
        $reflection = new \ReflectionClass($class);

        $this->init($reflection);
    }

    private function init(\ReflectionClass $reflection): void
    {
        if ($parent = $reflection->getParentClass()) {
            $this->init($parent);
        }

        if ($reflection->getConstructor()?->isPublic()) {
            $this->constructor = $reflection->getConstructor();
        }

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
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
            $returnTypeName = $method->getReturnType()?->getName();
            if ((
                    $reflection->getName() === $returnTypeName
                    or 'self' === $returnTypeName
                    or 'static' === $returnTypeName
                )
                and $method->isStatic()
            ) {
                $this->generators[$method->getName()] = $method;
            }
        }

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $this->properties[$property->getName()] = $property;
        }
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getConstructor(): \ReflectionMethod|null
    {
        return $this->constructor;
    }

    public function getGenerators(): array
    {
        return $this->generators;
    }

    public function getGetters(): array
    {
        return $this->getters;
    }

    public function getSetters(): array
    {
        return $this->setters;
    }

    public function getGettablePaths(): array
    {
        $propertyNames = array_keys($this->getProperties());
        $functionNames = array_keys($this->getGetters());
        return array_merge($propertyNames, $functionNames);
    }

    public function getSettablePaths(): array
    {
        $propertyNames = array_keys($this->getProperties());
        $functionNames = array_keys($this->getSetters());
        return array_merge($propertyNames, $functionNames);
    }
}
