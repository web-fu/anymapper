<?php

declare(strict_types=1);

namespace WebFu\Proxy;

use WebFu\Analyzer\AnalyzerFactory;
use WebFu\Analyzer\AnalyzerInterface;
use WebFu\Analyzer\ElementType;

class Proxy
{
    /** @var mixed[]|object */
    private array|object $element;
    private AnalyzerInterface $analyzer;

    /**
     * @param mixed[]|object $element
     */
    public function __construct(object|array $element)
    {
        $this->element = $element;
        $this->analyzer = AnalyzerFactory::create($element);
    }

    public function getAnalyzer(): AnalyzerInterface
    {
        return $this->analyzer;
    }

    public function get(string $path): mixed
    {
        $pathTracks = explode('.', $path);
        $track = array_shift($pathTracks);
        $trackList = $this->analyzer->getOutputTrackList();
        if (!array_key_exists($track, $trackList)) {
            throw new ProxyException($track.' gettable not found');
        }

        $index = $trackList[$track];

        $value = match ($index->getType()) {
            ElementType::PROPERTY => $this->element->{$index->getName()},
            ElementType::METHOD => call_user_func([$this->element, $index->getName()]),
            ElementType::NUMERIC_INDEX, ElementType::STRING_INDEX => $this->element[$index->getName()],
        };

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

        $endpoint = $this->element;
        if (count($pathTracks)) {
            /** @var mixed[]|object $endpoint */
            $endpoint = $this->get(implode('.', $pathTracks));
        }
        $endpointAnalyzer = AnalyzerFactory::create($endpoint);

        $trackList = $endpointAnalyzer->getInputTrackList();

        if (!array_key_exists($track, $trackList)) {
            throw new ProxyException($track.' settable not found');
        }

        $index = $trackList[$track];

        match ($index->getType()) {
            ElementType::PROPERTY => $endpoint->{$index->getName()} = $value,
            ElementType::METHOD => call_user_func([$endpoint, $index->getName()], $value),
            ElementType::NUMERIC_INDEX, ElementType::STRING_INDEX => $endpoint[$index->getName()] = $value,
        };
    }
}
