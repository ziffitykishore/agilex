<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Model\Generator\Cache;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ShippingProvider
 * @package RocketWeb\ShoppingFeeds\Model\Generator\Cache
 */
class ShippingProvider
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\CollectionFactory
     */
    protected $shippingCollectionFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\Collection
     */
    protected $shippingCollection;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache
     */
    protected $cache;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var int
     */
    protected $cacheTtl = 0;

    /**
     * @var array
     */
    protected $cacheTemporaryData = [];

    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\CollectionFactory $shippingCollectionFactory,
        \RocketWeb\ShoppingFeeds\Model\Product\ShippingFactory $shippingFactory
    )
    {
        $this->shippingCollectionFactory = $shippingCollectionFactory;
        $this->shippingFactory = $shippingFactory;
        $this->cache = $cache;
    }

    public function getShipping($adapter)
    {
        $adapterClassName = get_class($adapter);
        $adapterCacheKey = ['shipping', 'adapter', $adapterClassName];
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Shipping $shipping */
        if (($shipping = $this->cache->getCache($adapterCacheKey, false)) === false) {
            $shipping = $this->shippingFactory->create(['adapter' => $adapter]);
            $this->cache->setCache($adapterCacheKey, $shipping);
        }
        return $shipping;
    }

    public function prepareCache(
        \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface $adapter,
        \Magento\Catalog\Model\Product $product,
        $cacheTtl = 0
    )
    {
        $this->cacheTtl = $cacheTtl;
        $this->shippingCollection = $this->shippingCollectionFactory->create();
        $this->shippingCollection->filterByFeed($adapter->getFeed())
            ->filterByProduct($product)
            ->filterByStore($adapter->getStore())
            ->filterByCurrencyCode($adapter->getStore()->getCurrentCurrency()->getCode());

        $this->setTemporaryCacheData($adapter, $product);
        return $this;
    }

    public function setTemporaryCacheData(
        \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface $adapter,
        \Magento\Catalog\Model\Product $product
    )
    {
        $this->cacheTemporaryData = [
            'adapter' => $adapter,
            'product' => $product
        ];
        return $this;
    }

    public function getCache()
    {
        /** @var \DateTime $date */
        $date = $this->cacheTemporaryData['adapter']->getTimezone()->date();
        $date->sub(new \DateInterval(sprintf('P%sD', $this->cacheTtl)));
        $date = $date->format('Y-m-d H:i:s');
        $collection = clone $this->shippingCollection;
        $collection->filterByDate($date)
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            $cacheItem = $collection->getFirstItem();
            return $cacheItem->getValue();
        }
        return false;
    }

    public function setCache($value)
    {
        // We only save cache if its enabled & longer then 0
        if ($this->cacheTtl > 0) {
            /** @var \RocketWeb\ShoppingFeeds\Model\Shipping $cacheItem */
            $cacheItem = $this->shippingCollection->setPageSize(1)
                ->getFirstItem();
            if (!$cacheItem->getId()) {
                /**
                 * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface $adapter
                 * @var \Magento\Catalog\Model\Product $product
                 */
                extract($this->cacheTemporaryData);
                $cacheItem->setProductId($product->getId())
                    ->setStoreId($adapter->getStore()->getStoreId())
                    ->setFeedId($adapter->getFeed()->getId())
                    ->setCurrencyCode($adapter->getStore()->getCurrentCurrency()->getCode());
            }
            // The value might be the same but cache could expire, so we need to set the new date
            // Which doesn't happen if tha origData == data
            $date = $this->cacheTemporaryData['adapter']->getTimezone()->date();
            $date = $date->format('Y-m-d H:i:s');

            $cacheItem->setUpdatedAt($date)
                ->setValue($value)
                ->save();
        }

        return $this;
    }

    /**
     * Clear cache per feed
     * 
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @return \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider
     */
    public function clearCache(\RocketWeb\ShoppingFeeds\Model\Feed $feed)
    {
        $shippingCollection = $this->shippingCollectionFactory->create();
        $shippingCollection->filterByFeed($feed);
        foreach ($shippingCollection as $item) {
            $item->delete();
        }
        return $this;
    }
}
