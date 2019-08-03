<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Helper;

class Cache
{

    /**
     * Application config object
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\CacheInvalidate\Model\PurgeCache
     */
    protected $purgeCache;
    
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * 
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\CacheInvalidate\Model\PurgeCache $purgeCache
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     */
    public function __construct(
    \Magento\PageCache\Model\Config $config,
        \Magento\CacheInvalidate\Model\PurgeCache $purgeCache,
        \Magento\Framework\App\CacheInterface $cacheManager
    )
    {
        $this->config = $config;
        $this->purgeCache = $purgeCache;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Purge cache for a specific product
     * @param type $productId
     */
    public function purgeCache($productId)
    {
        $this->cacheManager->clean('catalog_product_' . $productId);
        if ($this->config->getType() == \Magento\PageCache\Model\Config::VARNISH && $this->config->isEnabled()) {
            $this->purgeCache->sendPurgeRequest('catalog_product_' . $productId);
        }
    }

}
