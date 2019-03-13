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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Grouped;

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable\Availability as ConfigurableAvailability;

class Availability extends ConfigurableAvailability
{
    public function filter($cell)
    {
        if (!$this->getAdapter()->getFeed()->getConfig('grouped_add_out_of_stock')) {
            if ($cell == self::OUT_OF_STOCK) {
                return true;
            }
        }
        return false;
    }
}