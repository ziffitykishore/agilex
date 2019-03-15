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

/**
 * Returns a CSV (or custom separator) list
 * of given attributes values
 *
 * Class VariantAttributes
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable
 */
class VariantAttributes extends MapperAbstract
{
    /**
     * @param array $map
     * @return mixed|string
     * @throws \RocketWeb\ShoppingFeeds\Model\Exception
     */
    public function map(array $map = array())
    {
        $attributesCodes = $map['param'];

        if (count($attributesCodes) == 0) {
            return '';
        }

        $cell = $this->getAttributeValue($map, $this->getAdapter());
        $associatedProductAdapters = $this->getAdapter()->getData('associated_product_adapters');

        // Try get from associated products
        if ($cell == "" && count($associatedProductAdapters) > 0) {
            foreach ($associatedProductAdapters as $associatedProductAdapter) {
                $cell .= $this->getAttributeValue($map, $associatedProductAdapter);
            }
        }

        return str_replace(",", " /", $cell);
    }

    /**
     * @param array $map
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter
     * @return string
     * @throws \RocketWeb\ShoppingFeeds\Model\Exception
     */
    protected function getAttributeValue($map, $adapter)
    {
        $attributesCodes = $map['param'];
        $separator = $this->getAdapter()->getFeed()->getConfig('configurable_attribute_merge_value_separator');
        $product = $adapter->getProduct();

        $cell = '';
        // Try to match the proper attribute by looking at what product has loaded
        foreach ($attributesCodes as $attributeCode) {
            if (!empty($attributeCode) && $product->hasData($attributeCode)) {
                $attribute = $this->getAdapter()->getMapAttribute($attributeCode);
                $v = $this->getAdapter()->getFilter()->cleanField(
                    $adapter->getAttributeValue($product, $attribute),
                    $map
                );
                if ($v != "") {
                    $cell .= empty($cell) ? $v : $separator . $v;
                }
            }
        }

        return $cell;
    }
}