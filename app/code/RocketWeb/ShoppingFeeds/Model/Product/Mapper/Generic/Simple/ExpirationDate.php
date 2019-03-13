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

class ExpirationDate extends MapperAbstract
{
    public function map(array $params = array())
    {
        $days = intval($params['param']) - 1;
        $days = $days < 0 ? 0 : $days;
        $date = $this->getAdapter()->getTimezone()->date();
        $date->add(new \DateInterval(sprintf('P%sD', $days)));
        $date = $date->format('Y-m-d');
        return $this->getAdapter()->getFilter()->cleanField($date, $params);
    }
}