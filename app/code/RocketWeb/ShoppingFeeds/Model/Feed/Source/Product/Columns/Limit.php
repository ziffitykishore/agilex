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

namespace RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Columns;

use RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Columns;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Class Columns
 */
class Limit extends Columns
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config
     */
    protected $feedTypesConfig;

    /**
     * Limit constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->feedTypesConfig = $feedTypesConfig;
    }

    /**
     * Retrieve columns
     *
     * @return array
     */
    public function getColumns()
    {
        if ($this->columns !== null) {
            return $this->columns;
        }

        /* @var $model \RocketWeb\ShoppingFeeds\Model\Feed */
        $feed = $this->coreRegistry->registry('feed');
        $columns = $feed->getConfig('columns_product_columns');;
        $directives = $this->feedTypesConfig->getDirectives($feed->getData('type'));

        foreach ($directives as $key => $directive) {
            if (!$directive['allow_output_limit']) {
                unset($directives[$key]);
            }
        }

        foreach ($columns as $key => $column) {
            if (strpos($column['attribute'], 'directive_') !== false
                && strpos($column['attribute'], 'directive_') == 0
                && !in_array($column['attribute'], array_keys($directives)))
            {
                unset($columns[$key]);
            }
        }

        $this->columns = $columns;
        return $this->columns;
    }
}
