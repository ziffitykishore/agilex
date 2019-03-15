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

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;
use \RocketWeb\ShoppingFeeds\Model\Exception as FeedException;

/**
 * Gets shipping weight and appends unit
 *
 * Class ShippingWeight
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple
 */
class ShippingWeight extends MapperAbstract
{
    /**
     * @param array $params
     */
    public function map(array $params = array())
    {
        $params['attribute'] = 'weight';
        $unit = isset($params['param']) ? $params['param'] : \RocketWeb\ShoppingFeeds\Model\Feed\Source\Shipping\Weight::WEIGHT_UNIT_KILOGRAM;

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getAdapter()->getProduct();

        // Get weight attribute
        $weightAttribute = $this->getAdapter()->getMapAttribute($params);
        if ($weightAttribute === false) {
            throw new FeedException(
                new \Magento\Framework\Phrase(sprintf('Couldn\'t find attribute \'%s\'.', $params['attribute']))
            );
        }

        $weight = number_format((float)$this->getAdapter()->getAttributeValue($product, $weightAttribute), 2);
        $weight = $weight ? sprintf('%s %s', $weight, $unit) : '';

        return $weight;
    }
}
