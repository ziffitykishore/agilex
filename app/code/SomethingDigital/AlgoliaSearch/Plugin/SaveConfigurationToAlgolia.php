<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use Algolia\AlgoliaSearch\Model\IndicesConfigurator;

class SaveConfigurationToAlgolia
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var IndicesConfigurator */
    private $indicesConfigurator;

    public function __construct(
        StoreManagerInterface $storeManager, 
        IndicesConfigurator $indicesConfigurator
    ) {
        $this->storeManager = $storeManager;
        $this->indicesConfigurator = $indicesConfigurator;
    }
    
    public function afterAfterSave(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject)
    {
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()) {
                $this->indicesConfigurator->saveConfigurationToAlgolia($store->getId());
            }
        }
    }
}

