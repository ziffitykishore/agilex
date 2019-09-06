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

/**
 * It outputs the static value from param
 *
 * Class StaticValue
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple
 */
class StaticValue extends MapperAbstract
{
    /**
     * @param array $params
     * @return string
     */
    public function map(array $params = array())
    {
        $value = isset($params['param']) ? $params['param'] : '';
        return $this->getAdapter()->getFilter()->cleanField($value, $params);
    }
}