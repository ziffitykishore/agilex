<?php

namespace Creatuity\Nav\Model\Map\Provider;

class StaticCollectionProvider implements CollectionProviderInterface
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }
}
