<?php

declare(strict_types=1);

namespace WebFu\Proxy;

use WebFu\Analyzer\AnalyzerInterface;
use WebFu\Analyzer\ArrayAnalyzer;
use WebFu\Analyzer\ClassAnalyzer;

class Proxy
{
    private array|object $element;
    private AnalyzerInterface $analyzer;

    public function __construct(object|array $element)
    {
        $this->element = $element;
        $this->analyzer = $this->getAnalyzer($element);
    }

    public function get(string $path): mixed
    {
        $pathTracks = explode('.', $path);
        $track = array_shift($pathTracks);
        $gettable = $this->analyzer->getGettableMethod($track);
        $value = call_user_func([$this->analyzer, $gettable->getName()], $track);

        if (!count($pathTracks)) {
            return $value;
        }

        $proxy = new self($value);

        return $proxy->get(implode('.', $pathTracks));
    }

    public function set(string $path, mixed $value): void
    {
        $pathTracks = explode('.', $path);
        $track = array_pop($pathTracks);

        $endpoint = $this->get(implode('.', $pathTracks));
        $endpointAnalyzer = $this->getAnalyzer($endpoint);
        $settable = $endpointAnalyzer->getSettableMethod($track);

        call_user_func([$endpointAnalyzer, $settable->getName()], $track, $value);
    }

    /**
     * @param mixed[]|object $subject
     */
    protected function getAnalyzer(array|object $subject): AnalyzerInterface
    {
        if (is_object($subject)) {
            return new ClassAnalyzer($subject);
        }

        return new ArrayAnalyzer($subject);
    }
}
