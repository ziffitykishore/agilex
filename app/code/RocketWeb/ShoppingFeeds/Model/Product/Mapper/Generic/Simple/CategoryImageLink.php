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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple;

use Magento\Catalog\Model\CategoryFactory;
use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;

class CategoryImageLink extends MapperAbstract
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache
     */
    protected $cache;

    protected $categoryFactory;

    /**
     * CategoryImageLink constructor.
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Logger $logger
     * @param \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->cache = $cache;
        parent::__construct($logger);
    }

    public function map(array $params = array())
    {
        $image = '';
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getAdapter()->getProduct();
        $categoryIds = $product->getCategoryIds();
        if (count($categoryIds) > 0) {
            $cacheKey = implode('-', $categoryIds);
            if ($this->cache->getCache($cacheKey, true)) {
                /** @var \Magento\Catalog\Model\Category $category */
                foreach ($categoryIds as $categoryId) {
                    $category = $this->categoryFactory->create()->load($categoryId);
                    if (!$category->hasChildren() && ($imageUrl = $category->getImageUrl())) {
                        $this->cache->setCache($cacheKey, $imageUrl);
                        break;
                    }
                }
            }
            $image = $this->cache->getCache($cacheKey, '');
        }

        $this->getAdapter()->getFilter()->findAndReplace($image, $params['column']);
        return $image;
    }
}