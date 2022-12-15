<?php

const FUNCTIONS = [
    'WebFu\\Mapper\\camelcase_to_underscore' => 'Mapper/camelcase_to_underscore.php',
];

foreach (FUNCTIONS as $function => $file) {
    if (function_exists($function)) {
        continue;
    }

    require_once __DIR__.'/'.$file;
}
