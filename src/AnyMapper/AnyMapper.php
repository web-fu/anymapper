<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use stdClass;
use WebFu\AnyMapper\Strategy\StrategyInterface;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Proxy\Proxy;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;
    private StrategyInterface $strategy;

    public function __construct(StrategyInterface|null $strategy = null)
    {
        $this->strategy = $strategy ?: new StrictStrategy();
    }

    /**
     * @param mixed[]|object $source
     */
    public function map(array|object $source): self
    {
        $this->sourceProxy = new Proxy($source);

        return $this;
    }

    public function using(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @param mixed[]|object $destination
     */
    public function into(array|object $destination): self
    {
        $this->destinationProxy = new Proxy($destination);

        return $this;
    }

    /**
     * @param class-string $className
     */
    public function as(string $className): self
    {
        if (!class_exists($className)) {
            throw new MapperException('Class ' . $className . ' does not exist');
        }

        $destination = new $className();
        $this->destinationProxy = new Proxy($destination);

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function serialize(): array
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();

        $output = [];
        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $value = $this->sourceProxy->get((string) $trackName);
            if (
                is_array($value)
                || is_object($value)
            ) {
                $value = (new self())->map($value)->serialize();
            }
            $output[$trackName] = $value;
        }

        return $output;
    }

    public function run()
    {
        $sourceTracks = $this->sourceProxy->getAnalyzer()->getOutputTrackList();
        $destinationAnalyzer = $this->destinationProxy->getAnalyzer();

        foreach ($sourceTracks as $trackName => $sourceTrack) {
            $destinationTrack = $destinationAnalyzer->getInputTrack($trackName);

            if (! $destinationTrack) {
                continue;
            }

            $sourceValue = $this->sourceProxy->get($trackName);

            $destinationValue = $this->strategy->cast($sourceValue, $destinationTrack->getDataTypes());

            $this->destinationProxy->set($trackName, $destinationValue);
        }

        return $this->destinationProxy->getElement();
    }
}
