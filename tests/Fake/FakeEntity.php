<?php

declare(strict_types=1);

namespace WebFu\Tests\Fake;

class FakeEntity extends FakeParentEntity
{
    use FakeTrait;

    public mixed $public;
    protected mixed $protected;
    private mixed $private;

    public function __construct()
    {
    }

    public function getPublic(): void
    {
    }

    public function isPublic(): void
    {
    }

    public function __get(string $key): void
    {
        $this->getPrivate();
    }

    public function setPublic(): void
    {
    }

    public function __set(string $key, mixed $value): void
    {
    }

    public static function createStatic(): static
    {
        return new static();
    }

    public static function createSelf(): self
    {
        return new self();
    }

    public static function create(): FakeEntity
    {
        return new FakeEntity();
    }

    public function getty(): void
    {
    }

    protected function getProtected(): void
    {
    }

    private function getPrivate(): void
    {
    }
}
