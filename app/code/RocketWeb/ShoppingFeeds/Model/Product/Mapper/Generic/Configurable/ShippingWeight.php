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

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;
use \RocketWeb\ShoppingFeeds\Model\Exception as FeedException;

/**
 * Gets shipping weight and appends unit
 *
 * Class ShippingWeight
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable
 */
class ShippingWeight extends MapperAbstract
{
    /**
     * @param array $params
     */
    public function map(array $params = array())
    {
        $params['attribute'] = 'weight';
        $unit = $params['param'];

        $associatedProductAdapters = $this->getAdapter()->getData('associated_product_adapters');

        $weights = [];
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $associatedProductAdapter */
        foreach ($associatedProductAdapters as $associatedProductAdapter)
        {
            // Get weight attribute
            $weightAttribute = $this->getAdapter()->getMapAttribute($params);
            if ($weightAttribute !== false) {
                $weights[] = number_format((float)$this->getAdapter()->getAttributeValue($associatedProductAdapter->getProduct(), $weightAttribute), 2);
            }
        }

        $weight = 0;
        if (count($weights) > 0) {
            $weight = min($weights);
        }
        $weight = $weight ? sprintf('%s %s', $weight, $unit) : '';

        return $weight;
    }
}



