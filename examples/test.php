<?php

require __DIR__ . '/../vendor/autoload.php';

$reflector = new \PHPStan\Reflection\ClassReflection(\WebFu\Tests\Fake\EntityWithAnnotation::class);
$reflector->getProperty('array');
