<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickOrder
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickOrder\Controller\Items;

use Exception;
use Magento\Bundle\Model\Option as bundleOption;
use Magento\Bundle\Model\Product\Type as bundleType;
use Magento\Catalog\Model\Product as Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\GroupedProduct\Model\Product\Type\Grouped as Grouped;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\QuickOrder\Helper\Data;
use Mageplaza\QuickOrder\Helper\Item as QodItemHelper;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Class Preitem
 * @package Mageplaza\QuickOrder\Controller\Items
 */
class Preitem extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var QodItemHelper
     */
    protected $_itemhelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storemanager;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    protected $grouped;

    /**
     * @var Magento\Bundle\Model\Option
     */
    protected $bundleOption;

    /**
     * @var Magento\Bundle\Model\Product\Type
     */
    protected $bundleType;

    /**
     * Preitem constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param JsonHelper $jsonHelper
     * @param QodItemHelper $itemhelper
     * @param Product $product
     * @param Grouped $grouped
     * @param bundleOption $bundleOption
     * @param bundleType $bundleType
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData,
        StoreManagerInterface $storeManager,
        JsonHelper $jsonHelper,
        QodItemHelper $itemhelper,
        Product $product,
        Grouped $grouped,
        bundleOption $bundleOption,
        bundleType $bundleType
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helperData = $helperData;
        $this->_storemanager = $storeManager;
        $this->_jsonHelper = $jsonHelper;
        $this->_itemhelper = $itemhelper;
        $this->product = $product;
        $this->grouped = $grouped;
        $this->bundleOption = $bundleOption;
        $this->bundleType = $bundleType;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $store = $this->_helperData->getStore();
        $group = $this->_helperData->getCustomerGroupId();
        $data = $this->getRequest()->getParam('value');

        if (!$data) {
            return $this->getResponse()->setBody(false);
        }
        $preItem = [];
        if ($data) {
            try {
                foreach ($data as $key => $value) {
                    if ($value !== "") {
                        $value_array = explode(',', $value);
                        if (count($value_array) == 1) {
                            $value_array[] = 1;
                        }
                    }
                    $customOption = [];
                    $customOptionValue = [];
                    $getProductInfoCollection = $this->_itemhelper->getProductCollectionForStore($value_array[0], $store, $group);
                    /** isset sku and qty input*/
                    if (isset($value_array[1]) && (intval($value_array[1]) > 0)) {
                        /** Check request item not meet all conditions filter of getProductCollectionForStore but maybe out of stock*/
                        if (!sizeOf($getProductInfoCollection)) {
                            $preItem[] = $this->_itemhelper->getPreItemNotMeetConditionsFilter($value_array[0], $value_array[1]);
                        } else {
                            /** Check request item meet all conditions filter*/
                            foreach ($getProductInfoCollection as $info) {
                                $productName = $info->getName();
                                $getFinalPrice = $info->getFinalPrice();
                                $productId = $info->getId();
                                $productSKU = $info->getSku();
                                $typeId = $info->getTypeId();
                            }
                            $customOptions = $this->_itemhelper->getCustomOptions($productId);

                            if (!empty($customOptions)) {
                                $customOptionValue = $customOptions[0];
                                $customOption = $customOptions[1];
                            }
                            if ($typeId == 'configurable') {
                                /** validate options item input before add preItem array*/
                                $options = [];
                                $optionIds = [];
                                $valueCheck = $value_array;
                                $count = 0;
                                $attributeOption = $this->_itemhelper->getProductAttributeOptions($productId);
                                $no_option = sizeof($attributeOption);
                                $sizeOf = sizeof($valueCheck);
                                /** case customer only input configuration product only sku,qty size <= 2*/
                                if ($sizeOf < 3) {
                                    /**validate value and attribute code of product*/
                                    $getProductOptionDefault = $this->_itemhelper->getProductOptionDefaultValue($attributeOption);
                                    $getOptionIdsDefaultValue = $this->_itemhelper->getOptionIdsDefaultValue($attributeOption);
                                    $getSelectValueDefault = $this->_itemhelper->getSelectValueDefault($attributeOption, $attrcodeInput = '', $valueOfAttrCodeInput = '');
                                    $getSelectValueIdKey = $this->_itemhelper->getSelectValueIdKey($attributeOption);
                                    $productAttributeId = $this->_itemhelper->getOptionIdsDefaultParam($attributeOption);
                                    $product_children_simple = $this->_itemhelper->getchidrenSimpleProudctByAttribute($productAttributeId, $productId);
                                    $superAttribute = $this->_itemhelper->getSuperAttribute($attributeOption);
                                    $skuChild = '';
                                    $preItem[] = $this->_itemhelper->getPreItemDataArray($productId, $productName, $productSKU, $product_children_simple->getSku(), $value_array[1], $product_children_simple->getFinalPrice(), $store, $typeId, $getProductOptionDefault, $getOptionIdsDefaultValue, $getSelectValueDefault, $getSelectValueIdKey, $superAttribute, $customOption, $customOptionValue);
                                } else {
                                    /** case customer input configuration product have option sizeof >= 3*/
                                    $statusCheckAlloptions = true;
                                    $countOption = 0;
                                    $selectValueConvert = [];
                                    $productAttributeId = [];
                                    foreach ($valueCheck as $option) {
                                        $count++;
                                        if ($count >= 3) {
                                            /**validate value and attribute code of product*/
                                            $countOption++;
                                            $option_input = explode(':', $option);
                                            if (isset($option_input[0]) && isset($option_input[1])) {
                                                $attrcode = $option_input[0];
                                                $valueOfAttrCode = $option_input[1];
                                            } else {
                                                $attrcode = '';
                                                $valueOfAttrCode = '';
                                            }
                                            $getValidateCode = $this->_itemhelper->checkAttributeCode($attrcode, $attributeOption);
                                            $validateValueofAttrCode = $this->_itemhelper->checkValueOfAttributeCode($valueOfAttrCode, $attributeOption);
                                            $getcheckAttributeCodeId = $this->_itemhelper->getcheckAttributeCodeId($attrcode, $attributeOption);
                                            $getcheckIdValueOfAttributeCode = $this->_itemhelper->getcheckIdValueOfAttributeCode($valueOfAttrCode, $attributeOption);
                                            $getSelectValueDefault = $this->_itemhelper->getSelectValueDefault($attributeOption, $attrcode, $valueOfAttrCode);
                                            $selectValueConvert = $this->_itemhelper->getSelectValueConvertOption($attrcode, $valueOfAttrCode, $selectValueConvert, $getSelectValueDefault);
                                            $getSelectValueIdKey = $this->_itemhelper->getSelectValueIdKey($attributeOption);
                                            $superAttribute = $this->_itemhelper->getSuperAttribute($attributeOption);
                                            $options[] = $option;
                                            $optionIds[] = $getcheckAttributeCodeId . ':' . $getcheckIdValueOfAttributeCode;
                                            $productAttributeId += [$getcheckAttributeCodeId => $getcheckIdValueOfAttributeCode];
                                            if (!$getValidateCode || !$validateValueofAttrCode) {
                                                $statusCheckAlloptions = false;
                                            }
                                        }
                                    }
                                    /** convert option follow order option of product*/
                                    $orderAttribute = [];
                                    $optionIdsConvert = [];
                                    foreach ($superAttribute as $superA) {
                                        $itemArray = explode(':', $superA);
                                        $orderAttribute[] = $itemArray[0];
                                    }
                                    foreach ($orderAttribute as $orderA) {
                                        foreach ($optionIds as $optionIdA) {
                                            $itemArray = explode(':', $optionIdA);
                                            if ($itemArray[0] == $orderA) {
                                                $optionIdsConvert[] = $optionIdA;
                                            }
                                        }
                                    }
                                    /** end convert option follow order option of product*/
                                    $product_children_simple = $this->_itemhelper->getchidrenSimpleProudctByAttribute($productAttributeId, $productId);

                                    if ($statusCheckAlloptions && $countOption == $no_option) {
                                        $preItem[] = $this->_itemhelper->getPreItemDataArray($productId, $productName, $productSKU, $product_children_simple->getSku(), $value_array[1], $product_children_simple->getFinalPrice(), $store, $typeId, $options, $optionIdsConvert, $selectValueConvert, $getSelectValueIdKey, $superAttribute, $customOption, $customOptionValue);
                                    }
                                }
                            } elseif ($typeId == "grouped") {
                                $product = $this->product->load($productId);
                                $childs = $this->grouped->getAssociatedProducts($product);
                                $childProduct = [];
                                foreach ($childs as $child) {
                                    $productSimple = $this->product->load($child->getId());
                                    $childsProductName = $productSimple->getName();
                                    $childGetFinalPrice = $productSimple->getFinalPrice();
                                    $childProductId = $productSimple->getId();
                                    $childProductSKU = $productSimple->getSku();
                                    $childTypeId = $productSimple->getTypeId();
                                    $childQty = (int)$child->getQty();
                                    $childProduct[] = $this->_itemhelper->getPreItemDataArray($childProductId, $childsProductName, $childProductSKU, $skuChild = '', $childQty, $childGetFinalPrice, $store, $childTypeId, $options = '', $optionIds = '', $optionSelectValue = '', $getSelectValueIdKey = '', $superAttribute = '');
                                }
                                $preItem[] = $this->_itemhelper->getPreItemDataArray($productId, $productName, $productSKU, $skuChild = '', $value_array[1], $getFinalPrice, $store, $typeId, $options = '', $optionIds = '', $optionSelectValue = '', $getSelectValueIdKey = '', $superAttribute = '', $customOption, $customOptionValue, $childProduct);
                            } elseif ($typeId == 'bundle') {
                                if ($value_array[1] != '1') {
                                    return $this->prepareJson(['errors' => false]);
                                }
                                $product = $this->product->load($productId);
                                $options = $this->bundleOption->getResourceCollection()->setProductIdFilter($product->getId())->setPositionOrder();
                                $options->joinValues($store->getId());
                                $bundleOption = [];
                                $bundleProduct = [];
                                $bundleSelectOption = [];
                                foreach ($options as $option) {
                                    $bundleOption[] = $option->getData();
                                }
                                $selections = $this->bundleType->getSelectionsCollection($this->bundleType->getOptionsIds($product), $product);
                                $key = 0;
                                foreach ($selections as $selection) {
                                    $bundleProduct[$key] = $selection->getData();
                                    $bundleProduct[$key]['tier_price'] = $this->_itemhelper->getTierPrices($selection->getData('sku'), '', $selection->getData('type_id'));
                                    $key++;
                                }
                                $preItem[] = $this->_itemhelper->getPreItemDataArray($productId, $productName, $productSKU, $skuChild = '', $value_array[1], $getFinalPrice, $store, $typeId, $options = '', $optionIds = '', $optionSelectValue = '', $getSelectValueIdKey = '', $superAttribute = '', $customOption, $customOptionValue, $childProduct = '', $bundleOption, $bundleProduct, $bundleSelectOption);
                            } else {
                                /** simple product type*/
                                $preItem[] = $this->_itemhelper->getPreItemDataArray($productId, $productName, $productSKU, $skuChild = '', $value_array[1], $getFinalPrice, $store, $typeId, $options = '', $optionIds = '', $optionSelectValue = '', $getSelectValueIdKey = '', $superAttribute = '', $customOption, $customOptionValue);
                            }
                        }
                    }
                }

                return $this->prepareJson($preItem);
            } catch (Exception $e) {
                $writer = new Stream(BP . '/var/log/quickOrder.log');
                $logger = new Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());

                return $this->prepareJson(['errors' => $e->getMessage()]);
            }
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function prepareJson($data)
    {
        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($data));
    }
}
