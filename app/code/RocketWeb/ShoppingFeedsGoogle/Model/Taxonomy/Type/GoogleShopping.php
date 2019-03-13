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

namespace RocketWeb\ShoppingFeedsGoogle\Model\Taxonomy\Type;

use \RocketWeb\ShoppingFeeds\Model\Taxonomy\ProviderInterface;
use \RocketWeb\ShoppingFeeds\Model\Taxonomy\ProviderAbstract;

/**
 * Google Shopping Taxonomy Provider
 */
class GoogleShopping extends ProviderAbstract implements ProviderInterface
{
    const URL_FORMAT = 'http://www.google.com/basepages/producttype/taxonomy.%s.txt';

    protected $taxonomy;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;

    /**
     * GoogleShopping constructor.
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed $feed,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
    ) {
        $this->curlFactory = $curlFactory;
        parent::__construct($feed, $cache);
    }

    /**
     * Generates taxonomy list
     *
     * @return array
     */
    public function getTaxonomyList()
    {
        if (!is_null($this->taxonomy)) {
            return $this->taxonomy;
        }

        if ($this->getCacheKey() && $this->getCacheLifetime()) {
            $cache = $this->cache->load($this->getCacheKey());
        }

        if ($cache) {
            $this->taxonomy = unserialize($cache);
            return $this->taxonomy;
        }

        $taxonomyData = $this->getTaxonomyData();

        if (false === $taxonomyData) {
            return [];
        }

        array_walk($taxonomyData, function(&$taxonomy, $key) {
            $taxonomy = [
                'id' => $key,
                'label' => $taxonomy
            ];
        });

        if ($this->getCacheKey() && $this->getCacheLifetime()) {
            $this->cache->save(
                serialize($taxonomyData),
                $this->getCacheKey(),
                $this->getCacheTags(),
                $this->getCacheLifetime()
            );
        }

        $this->taxonomy = $taxonomyData;

        return $this->taxonomy;
    }

    /**
     * Get taxonomy data from the external URL and parse it into array
     *
     * @return array|bool
     */
    public function getTaxonomyData()
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => 15]);
        $curl->write(\Zend_Http_Client::GET, $this->getTaxonomyUrl(), '1.0');

        $response = $curl->read();

        if ($response === false) {
            return false;
        }
        $curl->close();

        return $this->parseTaxonomy($response);
    }

    /**
     * Get taxonomy URL
     *
     * @return string
     */
    public function getTaxonomyUrl()
    {
        $locale = $this->feed->getConfig('categories_locale');

        return sprintf(
            self::URL_FORMAT, $locale
        );
    }

    /**
     * Parse and filter taxonomy response
     *
     * @param string $response
     * @return array
     */
    protected function parseTaxonomy($response)
    {
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        $taxonomyData = explode("\n", $response);

        if (strpos($taxonomyData[0], '#') !== false) {
            unset($taxonomyData[0]);
        }

        return array_values(array_filter($taxonomyData));
    }
}