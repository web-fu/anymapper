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

namespace WebFu\Proxy;

use WebFu\Analyzer\AnalyzerFactory;
use WebFu\Analyzer\AnalyzerInterface;
use WebFu\Analyzer\TrackType;

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
        $this->element  = $element;
        $this->analyzer = AnalyzerFactory::create($element);
    }

    /**
     * @return mixed[]|array
     */
    public function getElement(): object|array
    {
        return $this->element;
    }

    public function getAnalyzer(): AnalyzerInterface
    {
        return $this->analyzer;
    }

    /**
     * @return int|float|bool|string|object|mixed[]|null
     */
    public function get(string $path): int|float|bool|string|object|array|null
    {
        $pathTracks = explode('.', $path);
        $track      = array_shift($pathTracks);

        if (!$index = $this->analyzer->getOutputTrack($track)) {
            throw new ProxyException($track.' gettable not found');
        }

        $value = match ($index->getSource()) {
            TrackType::PROPERTY => $this->element->{$index->getName()},
            TrackType::METHOD   => $this->element->{$index->getName()}(),
            /* @phpstan-ignore-next-line */
            TrackType::NUMERIC_INDEX, TrackType::STRING_INDEX => $this->element[$index->getName()],
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
        $track      = array_pop($pathTracks);

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
            TrackType::PROPERTY => $endpoint->{$index->getName()} = $value,
            TrackType::METHOD   => $endpoint->{$index->getName()}($value),
            /* @phpstan-ignore-next-line */
            TrackType::NUMERIC_INDEX, TrackType::STRING_INDEX => $endpoint[$index->getName()] = $value,
        };
    }
}
