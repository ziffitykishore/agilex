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
 * Class ProductOption
 * @package RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple
 */
class ProductOption extends MapperAbstract
{
    /**
     * @param array $params
     * @return string
     */
    public function map(array $params = array())
    {
        $values = array();
        if (isset($params['param'])) {
            $names = is_array($params['param']) ? $params['param'] : array($params['param']);
            $options = $this->getAdapter()->getOptionProcessor()->getOptions(array($params['column'] => $names));

            foreach ($options as $optionCollection) {
                foreach ($optionCollection as $option) {
                    $values[] = $option->getTitle();
                }
            }
        }

        $expr = implode(',', $values);
        return $this->getAdapter()->getFilter()->cleanField($expr, $params);
    }
}



