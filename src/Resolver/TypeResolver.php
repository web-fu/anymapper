<?php

declare(strict_types=1);

namespace WebFu\Resolver;

class TypeResolver
{
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return string[]
     */
    public function resolve(): array
    {
        $typesString = get_debug_type($this->value);
        $types = explode('|', $typesString);

        $types = array_map(function (string $type): string {
            if (str_starts_with($type, 'resource')) {
                $type = 'resource';
            }
            return $type;
        }, $types);

        return $types;
    }

    private function createRegex(): string
    {
        $name = '[a-zA-Z_]+[a-zA-Z_0-9]*';
        $fqnn = '(\\'.$name.')+';
        $variable = '$'.$name;
        $property = $fqnn.'::'.$variable;
        $method = $fqnn.'::'.$name.'\(\)';
        $constant = $fqnn.'(::)?'.$name;
        $function = $fqnn.'(:)?'.$name;

        return '';
    }
}