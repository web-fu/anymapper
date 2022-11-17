<?php

declare(strict_types=1);

namespace WebFu\Mapper;

class ClassAnalyzer
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
            if (($reflection->getName() === $method->getReturnType()?->getName())
                and $method->isAbstract()
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
}
