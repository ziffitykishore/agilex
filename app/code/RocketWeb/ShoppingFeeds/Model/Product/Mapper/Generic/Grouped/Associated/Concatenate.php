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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Grouped\Associated;

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Concatenate as SimpleConcatenate;

class Concatenate extends SimpleConcatenate
{
    protected function getAttributeValue($key, $attributeColumn, $values = [], $params = [])
    {
        $inheritColumnsFromParent = $this->getAdapter()
            ->getFeed()
            ->getConfig('grouped_associated_products', []);

        $values[$key] = '';
        if (in_array($attributeColumn, $inheritColumnsFromParent)) {
            $values[$key] = $this->getAdapter()->getParentAdapter()->getMapValue($params);
        } else {
            $values[$key] = $this->getAdapter()->getMapValue($params);
        }

        if (empty($values[$key])) {
            $values[$key] = $this->getAdapter()->mapEmptyValues($params);
        }

        return $values;
    }
}