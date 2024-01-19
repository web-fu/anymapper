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
    public int $integer;
    public float $float;
    public DateTime $updatedAt;
}

$source = [
    'integer'   => '1',
    'float'     => '0.5',
    'updatedAt' => '2022-12-01',
];

$destination = (new WebFu\AnyMapper\AnyMapper())
    ->map($source)
    ->using(
        new WebFu\AnyMapper\Strategy\SQLFetchStrategy()
    )
    ->as(MyClass::class)
    ->run();

echo $destination->integer.PHP_EOL; // 1
echo $destination->float.PHP_EOL; // 0.5
echo $destination->updatedAt->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
echo PHP_EOL;
