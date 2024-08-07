AnyMapper
==============================================================================================
[![Latest Stable Version](https://poser.pugx.org/web-fu/anymapper/v)](https://packagist.org/packages/web-fu/anymapper)
[![PHP Version Require](https://poser.pugx.org/web-fu/anymapper/require/php)](https://packagist.org/packages/web-fu/anymapper)
![Test status](https://github.com/web-fu/anymapper/actions/workflows/tests.yaml/badge.svg)
![Static analysis status](https://github.com/web-fu/anymapper/actions/workflows/static-analysis.yml/badge.svg)
![Code style status](https://github.com/web-fu/anymapper/actions/workflows/code-style.yaml/badge.svg)

### A library that allows to map objects and arrays into objects and arrays with strong type support and pattern detection.

AnyMapper can get a variety of data inputs (object, arrays and composite of both) and hydrate a destination object or array ensuring safe type handling for the data during the process.  
AnyMapper can detect and extract data from public properties, standard getter / setter, use constructors and class factories and optionally perform smart data casting (ie: from a string to a date time).  
AnyMapper will not interfere with private or protected properties or methods and cannot grant the resulting object is in a "valid state"

## Installation

web-fu/anymapper is available on [Packagist](https://packagist.org/packages/web-fu/anymapper) and can be installed
using [Composer](https://getcomposer.org/).

```bash
composer require web-fu/anymapper
```
> Requires PHP 8.1 or newer.

## Examples

### Simple Mapping
```php
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

// Fill an existing class
$destination = new MyClass();

(new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->into($destination)
    ->run();

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;

// Create a new object of a class
$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->as(MyClass::class)
    ->run();

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;
```

### Casting Strategy

```php
// Use a strategy to customize mapping
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
        (new \WebFu\AnyMapper\Strategy\AllowedCastingStrategy())
            ->allow('string', DateTime::class)
    )
    ->as(MyClass::class)
    ->run();

echo $destination->value->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
echo PHP_EOL;
```

### Casting through callbacks

```php
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
```

### DocBlock Type Support
```php
// Use a strategy to customize mapping
final class MyClass
{
    /** @var DateTime */
    public $value;
}

$source = [
    'value' => '2022-12-01',
];

$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(
        (new \WebFu\AnyMapper\Strategy\DocBlockDetectStrategy())
    )
    ->as(MyClass::class)
    ->run();

echo $destination->value->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
echo PHP_EOL;
```

### Serialization
```php
// Perform a standard serialization
final class Bar
{
    private string $element;

    public function getElement(): string
    {
        return $this->element;
    }

    public function setElement(string $element): Bar
    {
        $this->element = $element;

        return $this;
    }
}

final class Foo
{
    /** @var Bar[] */
    private array $bars;

    /**
     * @return array
     */
    public function getBars(): array
    {
        return $this->bars;
    }

    /**
     * @param array $bars
     * @return Foo
     */
    public function setBars(array $bars): Foo
    {
        $this->bars = $bars;
        return $this;
    }
}

$foo = new Foo();
$foo->setBars([
    (new Bar())->setElement('string'),
]);

$destination =  (new \WebFu\AnyMapper\AnyMapper())
    ->map($foo)
    ->serialize();

var_export($destination);
/*
array (
  'bars' =>
  array (
    0 =>
    array (
      'element' => 'string',
    ),
  ),
)
*/
```

See `/examples` folder for full examples
