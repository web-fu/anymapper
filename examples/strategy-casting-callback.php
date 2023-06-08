<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

final class MyClass
{
    public int $value;
}

$source = [
    'value' => true,
];

$destination =  (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(
        (new \WebFu\AnyMapper\Strategy\CallbackCastingStrategy())
            ->addMethod(
                'bool',
                'int',
                fn (bool $value) => (int) $value,
            )
    )
    ->as(MyClass::class)
    ->run();

echo $destination->value; // 1
