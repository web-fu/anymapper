<?php

require __DIR__ . '/../vendor/autoload.php';

class Bar
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
