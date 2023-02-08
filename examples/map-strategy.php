<?php

require __DIR__ . '/../vendor/autoload.php';

final class MyClass
{
    private int $int;

    public function setInt(int $int): void {
        $this->int = $int;
    }

    public function getInt(): int {
        return $this->int;
    }
}

$source = [
    'int' => 1,
];

$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(\WebFu\AnyMapper\StrictStrategy::class)
    ->as(MyClass::class);

echo $destination->getInt(); // 1
echo PHP_EOL;
