<?php

namespace Creatuity\Nav\Model\Map\Provider;

use Creatuity\Nav\Model\Factory\GenericFactory;

class DynamicCollectionProvider implements CollectionProviderInterface
{
    protected $attributeFilters;
    protected $collectionFactoryKey;
    protected $genericFactory;

    public function __construct(
        array $attributeFilters,
        $collectionFactoryKey,
        GenericFactory $genericFactory
    ) {
        $this->attributeFilters = $attributeFilters;
        $this->collectionFactoryKey = $collectionFactoryKey;
        $this->genericFactory = $genericFactory;
    }

    public function getCollection()
    {
        $collection = $this->getCollectionFactory()->create();

        foreach ($this->attributeFilters as $attributeFilter) {
            $collection = $collection->addAttributeToFilter(
                $attributeFilter->getAttribute(),
                $attributeFilter->getCondition()
            );
        }

        return $collection;
    }

    protected function getCollectionFactory()
    {
        if (!isset($this->collectionFactory)) {
            $this->collectionFactory = $this->genericFactory->get($this->collectionFactoryKey);
        }

        return $this->collectionFactory;
    }
}
