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

namespace RocketWeb\ShoppingFeedsGoogle\Model\Product\Mapper\Google\Simple;

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;

class IdentifierExists extends MapperAbstract
{
    const IDENTIFIER_FALSE = "FALSE";
    
    public function map(array $params = array())
    {
        $cacheMapValues = array();
        $identifiers = array_key_exists('param', $params) ? array_filter(explode(',', $params['param'])) : array();
        $identifiersToLoad = !empty($identifiers) ? $identifiers : array('brand', 'gtin', 'mpn');

        foreach ($this->getAdapter()->getFeed()->getColumnsMap() as $map) {
            foreach ($identifiersToLoad as $column) {
                if ($map['column'] == $column) {
                    $cacheMapValues[$column] = $this->getAdapter()->getMapValue($map);
                }
            }
        }
        // Default params, or empty: special case for Google spec - gtin and mpn exclude each other
        if (empty($identifiers) || $identifiers == array('brand', 'gtin', 'mpn')) {
            $identifiers = array('brand');
            // if gtin is missing, we'll check require mpn instead
            if (!array_key_exists('gtin', $cacheMapValues)
                || (array_key_exists('gtin', $cacheMapValues) && empty($cacheMapValues['gtin'])))
            {
                array_push($identifiers, 'mpn');
            }
        }

        $score = 0;
        foreach ($identifiers as $column) {
            if (array_key_exists($column, $cacheMapValues) && !empty($cacheMapValues[$column])) {
                $score++;
            }
        }

        return ($score == count($identifiers)) ? "" : self::IDENTIFIER_FALSE;
    }
}