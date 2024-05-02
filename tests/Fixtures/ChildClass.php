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

namespace WebFu\Tests\Fixtures;

/*
 * This class is created as example of the functionality of AnyMapper and for testing purposes.
 * This class SHOULD NOT be used as an example of design
 */
class ChildClass extends ParentClass
{
    use EntityTrait;

    /*
     * Public properties CAN be mapped
     */
    public string $public;
    /*
     * Protected and private properties MUST NOT be mapped
     */
    protected string $protected;
    private string $private;
    /*
     * The following properties can be mapped only by setter / constructor function
     */
    private string $bySetter;
    private string $byConstructor;
    private array $data;

    /*
     * Constructor function MUST be called as first function ONLY if not otherwise specified
     */
    public function __construct(string $byConstructor = 'byConstructor')
    {
        $this->byConstructor = $byConstructor.' is set by constructor';
    }

    /*
     * Magic setter and getter CAN be used for mapping even though their use is no longer recommended
     */
    public function __get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function getByConstructor(): string
    {
        return $this->byConstructor;
    }

    /*
     * Standard getter / setter functions
     */
    public function setBySetter(string $bySetter): void
    {
        $this->bySetter = $bySetter.' is set by setter';
    }

    public function getBySetter(): string
    {
        return $this->bySetter;
    }

    public function isPropertyTrue(): bool
    {
        return true;
    }

    /*
     * These functions look like getter and setters, but require a number of parameters that is not standard
     */
    public function getIRequireOneParameter(string $parameter): string
    {
        return $parameter;
    }

    public function setIRequireZeroParameters(): void
    {
    }

    public function setIRequireTwoParameters(string $param1, string $param2): void
    {
    }

    /*
     * The following functions return instances of the class and CAN be used for mapping ONLY if specified explicitly
     */
    public static function createStatic(): static
    {
        return new static();
    }

    public static function createSelf(): self
    {
        return new self();
    }

    public static function create(): self
    {
        return new self();
    }

    /*
     * The following functions CAN be used as getter / setter ONLY if specified explicitly
     */
    public function getty(): void
    {
    }

    public function setty(): void
    {
    }

    /*
     * The following functions MUST remain unreachable from the mapper
     */
    protected function getProtectedUnreachable(): void
    {
    }

    protected function setProtectedUnreachable(): void
    {
    }

    private function getPrivateUnreachable(): void
    {
    }

    private function setPrivateUnreachable(): void
    {
    }
}
