<?php

declare(strict_types=1);

namespace WebFu\Proxy;

use WebFu\Analyzer\AnalyzerFactory;
use WebFu\Analyzer\AnalyzerInterface;

use function WebFu\Mapper\camelcase_to_underscore;

class Proxy
{
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
        $gettable = $this->analyzer->getGettableMethod($track);
        if (!$gettable) {
            throw new ProxyException($track.' gettable not found');
        }
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

        $endpointAnalyzer = $this->analyzer;
        if ($pathTracks) {
            $endpoint = $this->get(implode('.', $pathTracks));
            $endpointAnalyzer = AnalyzerFactory::create($endpoint);
        }

        $settable = $endpointAnalyzer->getSettableMethod($track);
        if (!$settable) {
            throw new ProxyException($track.' settable not found');
        }

        call_user_func([$endpointAnalyzer, $settable->getName()], $track, $value);
    }

    public function getPathMap(): array
    {
        $gettableNames = $this->getAnalyzer()->getGettableNames();
        $settableNames = $this->getAnalyzer()->getSettableNames();

        $pathMap['get'] = $this->aliasList($gettableNames);
        $pathMap['set'] = $this->aliasList($settableNames);

        return $pathMap;
    }

    private function aliasList(array $names): array
    {
        $aliasList = [];
        foreach ($names as $name) {
            $underscoreName = camelcase_to_underscore($name);
            $aliasList[$underscoreName] = $name;

            preg_match('#^(get|is|set)(?P<name>[A-Z][\w]+)#', $name, $matches);

            if (isset($matches['name'])) {
                $underscoreName = camelcase_to_underscore($matches['name']);
                $aliasList[$underscoreName] = $name;
            }
        }

        return $aliasList;
    }
}
