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

class Availability extends MapperAbstract
{
    const OUT_OF_STOCK = 'out of stock';
    const IN_STOCK     = 'in stock';
    const AVAILABLE    = 'available for order';
    const PREORDER     = 'preorder';

    public static $allowedStockStatuses = [self::OUT_OF_STOCK, self::IN_STOCK, self::AVAILABLE, self::PREORDER];

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $status;

    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \Magento\CatalogInventory\Model\Stock\Status $status
    )
    {
        $this->status = $status;
        parent::__construct($logger);
    }

    public function map(array $params = array())
    {
        $cell = $this->getStockStatus($this->getAdapter());

        return $this->getAdapter()->getFilter()->cleanField($cell, $params);
    }

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter
     * @return string
     */
    protected function getStockStatus($adapter)
    {
        $cell = self::OUT_OF_STOCK;
        $product = $adapter->getProduct();

        if ($this->getAdapter()->getFeed()->getConfig('general_use_default_stock')) {
            $status = $this->status->load($product->getId());
            if ($status->getStockStatus()) {
                $cell = self::IN_STOCK;
            }
        } else {
            $column = ['attribute' => $this->getAdapter()->getFeed()->getConfig('general_stock_attribute_code')];
            $stockAttribute = $adapter->getMapAttribute($column);

            $stockStatus = trim(strtolower($adapter->getAttributeValue($product, $stockAttribute)));
            if (in_array($stockStatus, self::$allowedStockStatuses) === false) {
                $stockStatus = str_replace(' ', '_', $stockStatus);
                if (in_array($stockStatus, self::$allowedStockStatuses) === false) {
                    $stockStatus = self::OUT_OF_STOCK;
                }
            }

            $cell = $stockStatus;
        }
        return $cell;
    }

    public function filter($cell)
    {
        if (!$this->getAdapter()->getFeed()->getConfig('filters_add_out_of_stock')) {
            if ($cell == self::OUT_OF_STOCK) {
                return true;
            }
        }
        return false;
    }
}