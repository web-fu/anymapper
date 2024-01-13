<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

final class MyClass
{
    public string $bar;
    private string $foo;

    public function setFoo(string $foo): self
    {
        $this->foo = $foo.' and I was set in a setter';

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

(new WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->into($destination)
    ->run();

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;
