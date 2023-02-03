<?php

declare(strict_types=1);

namespace WebFu\Reflection;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class ExtendedReflectionClass extends ReflectionClass
{
    /** @var string[] */
    protected array $useStatements = [];
    protected bool $useStatementsParsed = false;

    /**
     * @return iterable<ExtendedReflectionProperty>
     */
    public function getProperties(int|null $filter = null): array
    {
        return array_map(function (ReflectionProperty $property) {
            return new ExtendedReflectionProperty($this->getName(), $property->getName());
        }, $this->getProperties($filter));
    }

    /**
     * @return string[]
     */
    public function getDocTags(): array
    {
        $docBlock = Reflection::sanitizeDocBlock($this);

        return explode(PHP_EOL, $docBlock);
    }

    /**
     * @return string[]|class-string[]
     */
    public function getTemplates(): array
    {
        $docBlock = Reflection::sanitizeDocBlock($this);
        preg_match_all('/@template\s(?<template>\w+)\sof\s(?<type>\w+)/', $docBlock, $matches);

        $namespace = $this->getNamespaceName();

        $templates = [];
        foreach ($matches['type'] as  $k => $type) {
            $templates[$matches['template'][$k]] = (new ReflectionClass($namespace.'\\'.$type))->getName();
        }

        return array_filter($templates);
    }

    /**
     * @return string[]
     */
    public function getUseStatements(): array
    {
        if (!$this->isUserDefined()) {
            throw new RuntimeException('Must parse use statements from user defined classes.');
        }

        if (!$this->useStatementsParsed) {
            $this->useStatements = $this->tokenizeSource();
            $this->useStatementsParsed = true;
        }

        return $this->useStatements;
    }

    /**
     * @return string[]
     */
    private function tokenizeSource(): array
    {
        $source = file_get_contents($this->getFileName());
        $tokens = token_get_all($source);

        $builtNamespace = '';
        $buildingNamespace = false;
        $matchedNamespace = false;

        $useStatements = [];
        $record = false;
        $currentUse = [
            'class' => '',
            'as' => ''
        ];

        foreach ($tokens as $token) {
            if ($token[0] === T_NAMESPACE) {
                $buildingNamespace = true;

                if ($matchedNamespace) {
                    break;
                }
            }

            if ($buildingNamespace) {
                if ($token === ';') {
                    $buildingNamespace = false;
                    continue;
                }

                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $builtNamespace .= $token[1];
                        break;
                }

                continue;
            }

            if (!is_array($token)) {
                if ($record) {
                    $useStatements[] = $currentUse;
                    $record = false;
                    $currentUse = [
                        'class' => '',
                        'as' => ''
                    ];
                }

                continue;
            }

            if ($token[0] === T_CLASS) {
                break;
            }

            if (strcasecmp($builtNamespace, $this->getNamespaceName()) === 0) {
                $matchedNamespace = true;
            }

            if ($token[0] === T_USE) {
                $record = 'class';
            }

            if ($token[0] === T_AS) {
                $record = 'as';
            }

            if ($record) {
                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                        $currentUse[$record] .= $token[1];
                        break;
                }
            }

            if ($token[2] >= $this->getStartLine()) {
                break;
            }
        }

        // Make sure the as key has the name of the class even
        // if there is no alias in the use statement.
        foreach ($useStatements as &$useStatement) {
            if (empty($useStatement['as'])) {
                $useStatement['as'] = basename($useStatement['class']);
            }
        }

        return $useStatements;
    }
}
