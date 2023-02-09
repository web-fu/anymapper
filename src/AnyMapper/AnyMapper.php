<?php

declare(strict_types=1);

namespace WebFu\AnyMapper;

use stdClass;
use WebFu\AnyMapper\Strategy\AbstractStrategy;
use WebFu\AnyMapper\Strategy\StrictStrategy;
use WebFu\Proxy\Proxy;

class AnyMapper
{
    private Proxy $sourceProxy;
    private Proxy $destinationProxy;
    private AbstractStrategy $strategy;

    public function __construct()
    {
        $this->strategy = new StrictStrategy();
    }

    /**
     * @param mixed[]|object $source
     */
    public function map(array|object $source): self
    {
        $this->sourceProxy = new Proxy($source);

        return $this;
    }

    public function using(AbstractStrategy $strategy): self
    {
        $this->strategy = $strategy;

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

    private function doMapping(): void
    {
        $this->strategy->init($this->sourceProxy, $this->destinationProxy)->run();
    }
}
