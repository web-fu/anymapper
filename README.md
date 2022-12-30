# AnyMapper
### A library that allows to map objects and arrays into objects and arrays with strong type support and pattern detection.

AnyMapper can get a variety of data inputs (object, arrays and composite of both) and hydrate a destination object or array ensuring safe type handling for the data during the process.

AnyMapper can detect and extract data from public properties, standard getter / setter, use constructors and class factories and optionally perform smart data casting (ie: from a string to a date time).

AnyMapper will not interfere with private or protected properties or methods and cannot grant the resulting object is in a "valid state"

## Note

This library is an Alpha version and should not be used in a production environment.

## Example

```php
final class MyClass
{
    private string $foo;
    public string $bar;
    private \DateTime $startingDate;

    public function setFoo(string $foo): MyClass {
        $this->foo = $foo . ' and I was set in a setter';
        return $this;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    /**
     * @return DateTime
     */
    public function getStartingDate(): DateTime
    {
        return $this->startingDate;
    }

    /**
     * @param DateTime $startingDate
     * @return MyClass
     */
    public function setStartingDate(DateTime $startingDate): MyClass
    {
        $this->startingDate = $startingDate;
        return $this;
    }
}

$source = [
    'foo' => 'I am foo',
    'bar' => 'I am bar',
    'startingDate' => '2022-12-01 00:00:00',
];

$destination = new MyClass();

(new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->allowDataCasting('string', DateTime::class)
    ->into($destination);

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;
echo $destination->getStartingDate()->format('Y-m-d'); // 2022-12-01
echo PHP_EOL;
```

See `/examples` folder for some examples