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

trait EntityTrait
{
    public mixed $trait;
    private mixed $traitProperty;

    public function getTraitProperty(): mixed
    {
        return $this->traitProperty;
    }

    public function setTraitProperty(mixed $traitProperty): void
    {
        $this->traitProperty = $traitProperty;
    }
}
