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

namespace RocketWeb\ShoppingFeeds\Model\Product\Formatter\Price;
use RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterAbstract;
/**
 * Class Currency
 * @package RocketWeb\ShoppingFeeds\Model\Product\Formatter\Price
 */
class Currency extends FormatterAbstract
{
    /**
     * @param $var
     * @return mixed
     */
    public function run($var)
    {
        return ($var > 0) ? sprintf("%.2F %s", $var, $this->getAdapter()->getData('store_currency_code')) : '';
    }
}