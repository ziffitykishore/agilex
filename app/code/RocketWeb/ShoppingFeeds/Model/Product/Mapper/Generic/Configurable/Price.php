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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable;
use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Price as SimplePrice;

/**
 * Class Price
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple
 */
class Price extends SimplePrice
{
    /**
     * @param array $params
     * @return string
     */
    public function map(array $params = array())
    {
        $key = $this->getKey(false, $params);

        return $this->getPrice($key);
    }

    protected function getPrice($key)
    {
        $allPrices = [];
        $associatedProductAdapters = $this->getAdapter()->getData('associated_product_adapters');

        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $associatedProductAdapter */
        foreach ($associatedProductAdapters as $associatedProductAdapter)
        {
            $prices = $associatedProductAdapter->getPrices();
            $allPrices[] = $prices[$key];
        }

        if (count($allPrices) == 0) {
            $this->getAdapter()->setSkipProduct(sprintf('Product skipped - no associated products found, price = 0. Product SKU #%s', $this->getAdapter()->getProduct()->getSku()));
            return 0;
        }
        return min($allPrices);
    }
}