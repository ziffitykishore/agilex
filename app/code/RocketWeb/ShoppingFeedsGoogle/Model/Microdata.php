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

namespace RocketWeb\ShoppingFeedsGoogle\Model;

use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Model\Product\Option as Option;
use \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract as Adapter;
use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable\Availability;

class Microdata extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Adapter Factory instance
     *
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory
     */
    protected $adapterFactory;

    /**
     * Feed Factory instance.
     *
     * @var \RocketWeb\ShoppingFeeds\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory,
        \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory $adapterFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);
        $this->feedFactory = $feedFactory;
        $this->adapterFactory = $adapterFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getMicrodata()
    {
        $row = $this->getRow();
        return $this->createRowObject($row);
    }

    /**
     * Retrieves all maps for current product - will include children maps if any
     *
     * @return array
     */
    public function getRow()
    {
        $product = $this->getProduct();
        $assocId = $this->getAssocId();
        $store = $this->getStore();
        $conditionAttribute = $this->getConditionAttribute();

        /** @var \RocketWeb\ShoppingFeeds\Model\Feed $feed */
        $feed = $this->feedFactory->create()->load($store->getId(), 'store_id');

        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter */
        $adapter = $this->adapterFactory->create($product, $feed);

        if ($assocId !== false) {
            $parentMap = new \Magento\Framework\DataObject();
            $parentMap->setProduct($this->getBlockProduct());
            $adapter->setParentMap($parentMap);
        }

        // init associated_product_adapters
        $adapter->beforeMap();

        return $this->mapProduct($product, $adapter, $conditionAttribute);
    }

    /**
     * Converts map array to microdata Object
     *
     * @param array $map map array returned by the generator
     * @return null|\Magento\Framework\DataObject
     */
    protected function createRowObject($map)
    {
        if (empty($map['price']) || empty($map['availability']) || empty($map['title'])) {
            return null;
        }

        $microdata = new \Magento\Framework\DataObject();
        $microdata->setName($map['title']);
        $microdata->setSku($map['sku']);

        if (!empty($map['sale_price'])) {
            $price = $map['sale_price'];
        }
        else {
            $price = $map['price'];
        }

        $microdata->setPrice(\Zend_Locale_Format::toNumber($price, array(
            'precision' => 2,
            'number_format' => '#0.00'
        )));

        $microdata->setCurrency($map['currency']);

        if ($map['availability'] == Availability::IN_STOCK) {
            $microdata->setAvailability('http://schema.org/InStock');
        }
        else {
            $microdata->setAvailability('http://schema.org/OutOfStock');
        }

        if (array_key_exists('condition', $map)) {
            if (strcasecmp('new', $map['condition']) == 0) {
                $microdata->setCondition('http://schema.org/NewCondition');
            }
            else if (strcasecmp('used', $map['condition']) == 0) {
                $microdata->setCondition('http://schema.org/UsedCondition');
            }
            else if (strcasecmp('refurbished', $map['condition']) == 0) {
                $microdata->setCondition('http://schema.org/RefurbishedCondition');
            }
        }

        return $microdata;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter
     * @param null $conditionAttribute
     * @return array
     */
    protected function mapProduct(Product $product, Adapter $adapter, $conditionAttribute = '')
    {
        $condition = 'new';
        $includeTax = $this->getIncludeTax();

        if ($conditionAttribute) {
            $value = $this->eavConfig->getAttribute('catalog_product', $conditionAttribute)
                ->getFrontend()->getValue($product);
            if (!empty($value) && in_array($value, array('new', 'used', 'refurbished'))) {
                $condition = $value;
            }
        }

        $map = array(
            'sku' => $adapter->getMapValue(array('attribute' => 'sku', 'column' => 'sku', 'param' => '')),
            'title' => $adapter->getMapValue(array('attribute' => 'name', 'column' => 'title', 'param' => '')),
            'price' => $adapter->getMapValue(array('attribute' => 'directive_price', 'column' => 'price', 'param' => $includeTax)),
            'sale_price' => $adapter->getMapValue(array('attribute' => 'directive_sale_price', 'column' => 'sale_price', 'param' => $includeTax)),
            'availability' => $adapter->getMapValue(array('attribute' => 'directive_availability', 'column' => 'availability', 'param' => '')),
            'condition' => $condition,
            'currency' => $adapter->getFeed()->getConfig('general_currency'),
        );

        $optionPrice = $this->getOptionPrice($product);
        $map['price'] += $optionPrice;
        if (!empty($map['sale_price'])) {
            $map['sale_price'] += $optionPrice;
        }

        return $map;
    }

    /**
     * @param Product $product
     * @return int
     */
    protected function getOptionPrice(Product $product)
    {
        $price = 0;
        $productOptions = $product->getOptions();

        if (count($productOptions) > 0) {
            // the request data will be passed to $this from the block
            $requestParams = $this->getRequestParams();

            /** @var Product\Option $option */
            foreach ($productOptions as $option) {
                $type = $option->getType();

                if ($type == Option::OPTION_TYPE_DROP_DOWN) {
                    $type = Option::OPTION_GROUP_SELECT;
                }

                $key = $type. '_'. $option->getId();
                if (array_key_exists($key, $requestParams)) {
                    $valueId = $requestParams[$key];

                    /** @var Product\Option\Value $values */
                    $values = $option->getValues();

                    foreach ($values as $value) {
                        if ($valueId == $value->getId()) {
                            $price += $value->getPrice(true);
                        }
                    }
                }
            }
        }

        return $price;
    }
}