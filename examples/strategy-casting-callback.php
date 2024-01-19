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

require __DIR__.'/../vendor/autoload.php';

final class MyClass
{
    public int $value;
}

$source = [
    'value' => true,
];

$destination = (new WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(
        (new WebFu\AnyMapper\Strategy\CallbackCastingStrategy())
            ->addMethod(
                'bool',
                'int',
                fn (bool $value) => (int) $value,
            )
    )
    ->as(MyClass::class)
    ->run();

echo $destination->value; // 1
