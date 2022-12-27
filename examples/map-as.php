<?php

require __DIR__ . '/../vendor/autoload.php';

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

$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->allowDataCasting([
        'string' => DateTime::class,
    ])
    ->as(MyClass::class);

echo $destination->getFoo(); // I am foo and I was set in a setter
echo PHP_EOL;
echo $destination->bar; // I am bar;
echo PHP_EOL;
echo $destination->getStartingDate()->format('Y-m-d'); // 2022-12-01
echo PHP_EOL;
