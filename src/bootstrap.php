<?php

declare(strict_types=1);

const FUNCTIONS = [
    'WebFu\\Internal\\reflection_type_names' => 'Internal/reflection_type_names.php',
    'WebFu\\Internal\\camelcase_to_underscore' => 'Internal/camelcase_to_underscore.php',
    'WebFu\\Internal\\get_type' => 'Internal/get_type.php',
];

foreach (FUNCTIONS as $function => $file) {
    if (function_exists($function)) {
        continue;
    }

    require_once __DIR__.'/'.$file;
}
