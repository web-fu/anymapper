<?php

declare(strict_types=1);

namespace WebFu\Tests\Benchmark\AnyMapper;

class AnyMapperBench
{
    /**
     * @Revs(1000)
     */
    public function benchMapInto(): void
    {
        $source = [
            'foo' => 'I am foo',
            'bar' => 'I am bar',
        ];

        $destination = new class () {
            private string $foo;
            public string $bar;

            public function setFoo(string $foo): self
            {
                $this->foo = $foo . ' and I was set in a setter';
                return $this;
            }

            public function getFoo(): string
            {
                return $this->foo;
            }
        };

        (new \WebFu\AnyMapper\AnyMapper())
            ->map($source)
            ->into($destination);
    }
}
