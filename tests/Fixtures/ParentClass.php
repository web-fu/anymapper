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

class ParentClass
{
    public mixed $parent;
    private mixed $parentProperty;

    public function getParentProperty(): mixed
    {
        return $this->parentProperty;
    }

    public function setParentProperty(mixed $parentProperty): void
    {
        $this->parentProperty = $parentProperty;
    }
}
