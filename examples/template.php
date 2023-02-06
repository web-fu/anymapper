<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * @template DT of DateTime
 */
final class MyClass
{
    /** @var DT */
    private $DT;

    /**
     * @return DT
     */
    public function getValue()
    {
        return $this->DT;
    }

    public function setValue($DT): MyClass
    {
        $this->DT = $DT;
        return $this;
    }
}

$source = [
    'value' => '2022-12-01 00:00:00',
];

$destination = (new \WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->allowDataCasting('string', DateTime::class)
    ->as(MyClass::class);

echo $destination->getValue()->format('Y-m-d'); // 2022-12-01
echo PHP_EOL;
