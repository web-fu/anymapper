<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

final class MyClass
{
    public DateTime $value;
}

$source = [
    'value' => '2022-12-01',
];

$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(
        (new \WebFu\AnyMapper\Strategy\AutodetectStrategy())
    )
    ->as(MyClass::class);

echo $destination->value->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
echo PHP_EOL;
