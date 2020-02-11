<?php

namespace SomethingDigital\AlgoliaSearch\Helper;

use Algolia\AlgoliaSearch\Helper\Data as AlgoliaData;
use SomethingDigital\AlgoliaSearch\Helper\Entity\AttributeOptionHelper;
use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\Logger;

class Data
{
    private $helperData;
    private $attributeOptionHelper;
    private $algoliaHelper;
    private $logger;

    public function __construct(
        AlgoliaData $helperData,
        AttributeOptionHelper $attributeOptionHelper,
        AlgoliaHelper $algoliaHelper,
        Logger $logger
    ) {
        $this->helperData = $helperData;
        $this->attributeOptionHelper = $attributeOptionHelper;
        $this->algoliaHelper = $algoliaHelper;
        $this->logger = $logger;
    }

    public function rebuildAttributeOptionIndex()
    {
        if ($this->helperData->isIndexingEnabled() === false) {
            return;
        }

        $indexName = $this->helperData->getIndexName($this->attributeOptionHelper->getIndexNameSuffix());

        $attrOptions = $this->attributeOptionHelper->getAttributesOptions();

        foreach (array_chunk($attrOptions, 100) as $chunk) {
            try {
                $this->algoliaHelper->addObjects($chunk, $indexName . '_tmp');
            } catch (\Exception $e) {
                $this->logger->log($e->getMessage());
                continue;
            }
        }

        $this->algoliaHelper->moveIndex($indexName . '_tmp', $indexName);
        $this->algoliaHelper->setSettings($indexName, $this->attributeOptionHelper->getIndexSettings());
    }
}
