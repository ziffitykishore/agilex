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

namespace RocketWeb\ShoppingFeeds\Model\Taxonomy;

/**
 * Class ProviderAbstract
 */
class ProviderAbstract extends \Magento\Framework\DataObject
{
    const CACHE_KEY = 'TAXONOMY_CACHE';

    const CACHE_TAG = 'CONFIG';

    const CACHE_LIFETIME = 2592000;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed
     */
    protected $feed;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * ProviderAbstract constructor.
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param array $data
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed $feed,
        \Magento\Framework\App\CacheInterface $cache,
        array $data = []
    ) {
        $this->feed = $feed;
        $this->cache = $cache;

        parent::__construct($data);
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return self::CACHE_LIFETIME;
    }

    /**
     * Retrieve cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return [self::CACHE_TAG];
    }

    /**
     * Retrieve cache key
     *
     * @return string
     */
    public function getCacheKey()
    {
        return implode('_', [
            self::CACHE_KEY,
            $this->feed->getType(),
            $this->feed->getConfig('categories_locale')
        ]);
    }
}