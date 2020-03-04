<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use Algolia\AlgoliaSearch\Model\IndicesConfigurator;
use Magento\Framework\App\CacheInterface;
use SomethingDigital\ReactPlp\ViewModel\ReactPlp;

class SaveConfigurationToAlgolia
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var IndicesConfigurator */
    private $indicesConfigurator;

    /** @var Cache */
    private $cache;

    public function __construct(
        StoreManagerInterface $storeManager, 
        IndicesConfigurator $indicesConfigurator,
        CacheInterface $cache
    ) {
        $this->storeManager = $storeManager;
        $this->indicesConfigurator = $indicesConfigurator;
        $this->cache = $cache;
    }
    
    public function afterAfterSave(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject)
    {
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()) {
                $this->indicesConfigurator->saveConfigurationToAlgolia($store->getId());
                $this->cache->remove(ReactPlp::LIST_ATTRIBUTES_CACHE_ID);
                $this->cache->remove(ReactPlp::TABLE_ATTRIBUTES_CACHE_ID);
                $this->cache->remove(ReactPlp::FILTER_ATTRIBUTES_CACHE_ID);
            }
        }
    }
}

