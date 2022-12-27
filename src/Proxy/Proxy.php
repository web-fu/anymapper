<?php

declare(strict_types=1);

namespace WebFu\Proxy;

use WebFu\Analyzer\AnalyzerFactory;
use WebFu\Analyzer\AnalyzerInterface;
use WebFu\Analyzer\ElementSource;

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

        if (!$index = $this->analyzer->getOutputTrack($track)) {
            throw new ProxyException($track.' gettable not found');
        }

        $value = match ($index->getSource()) {
            ElementSource::PROPERTY => $this->element->{$index->getName()},
            ElementSource::METHOD => $this->element->{$index->getName()}(),
            ElementSource::NUMERIC_INDEX, ElementSource::STRING_INDEX => $this->element[$index->getName()],
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

        if (!$index = $endpointAnalyzer->getInputTrack($track)) {
            throw new ProxyException($track.' settable not found');
        }

        match ($index->getSource()) {
            ElementSource::PROPERTY => $endpoint->{$index->getName()} = $value,
            ElementSource::METHOD => $endpoint->{$index->getName()}($value),
            ElementSource::NUMERIC_INDEX, ElementSource::STRING_INDEX => $endpoint[$index->getName()] = $value,
        };
    }
}
