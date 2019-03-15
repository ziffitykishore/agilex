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

class Id extends MapperAbstract
{
    public function map(array $params = array())
    {
        $cell = $this->getAdapter()->getProduct()->getId();
        if ($params['param']) {
            $cell .= preg_replace('/[^a-zA-Z0-9]/', "", $this->getAdapter()->getStore()->getCode());
        }
        return $this->getAdapter()->getFilter()->cleanField($cell, $params);
    }
}