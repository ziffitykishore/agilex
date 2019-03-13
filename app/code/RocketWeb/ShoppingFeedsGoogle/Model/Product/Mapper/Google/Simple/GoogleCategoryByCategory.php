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

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\ProductTypeByCategory;

class GoogleCategoryByCategory extends ProductTypeByCategory
{
    public function map(array $params = array())
    {
        $mapByCategory = $this->getSortedTaxonomyMap();
        $value = $this->matchByCategory($mapByCategory, $this->getAdapter()->getProduct()->getCategoryIds(), 'tx');

        $this->getAdapter()->getFilter()->findAndReplace($value, $params['column']);
        return html_entity_decode($value);
    }
}