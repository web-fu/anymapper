<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

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

    public function getBars(): array
    {
        return $this->bars;
    }

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
