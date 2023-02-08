<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use WebFu\Analyzer\Track;
use WebFu\Proxy\Proxy;
use stdClass;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;
    /** @var array<string[]> */
    private array $allowedDataCasting = [];
    private string $strategyClass = StrictStrategy::class;

    /**
     * @param mixed[]|object $source
     */
    public function map(array|object $source): self
    {
        $this->sourceProxy = new Proxy($source);

        return $this;
    }

    /**
     * @param class-string<StrategyInterface> $strategyClass
     */
    public function using(string $strategyClass): self
    {
        $this->strategyClass = $strategyClass;

        return $this;
    }

    /**
     * @param mixed[]|object $destination
     */
    public function into(array|object $destination): void
    {
        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();
    }

    /**
     * @param class-string $className
     * @return object
     */
    public function as(string $className): object
    {
        if (!class_exists($className)) {
            throw new MapperException('Class ' . $className . ' does not exist');
        }
        $destination = new $className();

        if ($className === stdClass::class) {
            return (object) $this->serialize();
        }

        $this->destinationProxy = new Proxy($destination);
        $this->doMapping();

        return $destination;
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
            if (is_array($value) || is_object($value)) {
                $value = (new self())->map($value)->serialize();
            }
            $output[$trackName] = $value;
        }

        return $output;
    }

    public function allowDataCasting(string $from, string $to): self
    {
        $this->allowedDataCasting[$from][] = $to;

        return $this;
    }

    private function doMapping(): void
    {
        /** @var StrategyInterface $strategy */
        $strategy = new $this->strategyClass($this->sourceProxy, $this->destinationProxy, $this->allowedDataCasting);
        $strategy->run();
    }
}
