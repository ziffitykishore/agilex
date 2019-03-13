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

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Availability as SimpleAvailability;

class Availability extends SimpleAvailability
{
    public function map(array $params = array())
    {
        // First we process the parent adapter
        $parentCell = $this->getStockStatus($this->getAdapter());

        if ($parentCell == self::IN_STOCK) {
            $associatedProductAdapters = $this->getAdapter()->getData('associated_product_adapters');

            $cell = self::OUT_OF_STOCK;
            /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $associatedProductAdapter */
            foreach ($associatedProductAdapters as $associatedProductAdapter)
            {
                $associatedCell = $this->getStockStatus($associatedProductAdapter);

                if ($associatedCell == self::IN_STOCK) {
                    $cell = self::IN_STOCK;
                    break;
                }
            }

            if ($cell == self::OUT_OF_STOCK && count($associatedProductAdapters) > 0) {
                $parentCell = self::OUT_OF_STOCK;
            }
        }

        return $this->getAdapter()->getFilter()->cleanField($parentCell, $params);
    }
}