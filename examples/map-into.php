<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

final class MyClass
{
    private string $foo;
    public string $bar;

    public function setFoo(string $foo): MyClass {
        $this->foo = $foo . ' and I was set in a setter';
        return $this;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}

$source = [
    'foo' => 'I am foo',
    'bar' => 'I am bar',
];

$destination = new MyClass();

(new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->into($destination);

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;