<?php
/**
 * Catalog Rule Product Condition data model
 */

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Model\Rule\Condition;

use Magento\Catalog\Model\ProductCategoryList;
use Magento\CatalogRule\Model\Rule\Condition\Product as SimpleCondition;

/**
 * Class Quantity
 * @package Wyomind\Advancedinventory\Model\Rule\Condition
 */
class Quantity extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';
    /**
     * @var Wyomind\PointOfSale\Model\PointOfSale|Wyomind\PointOfSale\Model\PointOfSaleFactory
     */
    protected $pointOfSaleFactory;
    /**
     * @var \Wyomind\AdvancedInventory\Model\ResourceModel\StockFactory
     */
    protected $modelResourceStockFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Quantity constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Wyomind\PointOfSale\Model\PointOfSaleFactory $pointOfSaleFactory
     * @param \Wyomind\AdvancedInventory\Model\ResourceModel\StockFactory $modelResourceStockFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     * @param ProductCategoryList|null $categoryList
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $pointOfSaleFactory,
        \Wyomind\AdvancedInventory\Model\ResourceModel\StockFactory $modelResourceStockFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = [],
        ProductCategoryList $categoryList = null)
    {

        $this->pointOfSaleFactory = $pointOfSaleFactory;
        $this->modelResourceStockFactory = $modelResourceStockFactory;
        $this->request = $request;
        parent::__construct($context, $backendData, $config, $productFactory, $productRepository, $productResource, $attrSetCollection, $localeFormat, $data, $categoryList);

    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->request->getControllerModule() == NULL) {

            return SimpleCondition::class;
        }
        return self::class;
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {

        $pos = $this->pointOfSaleFactory->create()->getPlace($this->getAttribute())->getFirstItem();
        return __("Qty in") . " " . $pos->getName();
    }

    /**
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = array(
                '==' => __('is'),
                '!=' => __('is not'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
                '>' => __('greater than'),
                '<' => __('less than'),

            );
        }
        return $this->_defaultOperatorOptions;
    }

    /**
     * @param $productCollection
     * @param bool $id
     * @return $this
     */
    public function collectValidatedAttributes($productCollection, $id = false)
    {
        unset($productCollection);
        $this->_entityAttributeValues = array_map('floatval', $this->modelResourceStockFactory->create()->getItems($this->getAttribute(), $id));
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {

        if (!isset($this->_entityAttributeValues)) {
            $this->collectValidatedAttributes($object, $object->_getData('entity_id'));
        }
        if (isset($this->_entityAttributeValues[$object->_getData('entity_id')])) {
            $object->setData($this->_getData('attribute'), $this->_entityAttributeValues[$object->_getData('entity_id')]);
        }
        return parent::validate($object);

    }


}
