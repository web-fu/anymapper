<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        '@PHP80Migration:risky' => true,
    ])
    ->setFinder($finder);
