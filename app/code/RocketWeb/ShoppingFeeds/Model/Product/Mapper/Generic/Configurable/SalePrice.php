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

/**
 * Returns sale price if it exists (hasSpecialPrice())
 *
 * Class SalePrice
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable
 */
class SalePrice extends Price
{
    /**
     * @param array $params
     * @return string
     */
    public function map(array $params = array())
    {
        if (!$this->getAdapter()->hasSpecialPrice()) {
            return 0;
        }

        $key = $this->getKey(true, $params);

        return $this->getPrice($key);
    }
}