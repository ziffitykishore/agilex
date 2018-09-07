<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoRule
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoRule\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Product\AbstractProduct;

/**
 * Class Product
 * @package Mageplaza\SeoRule\Model\Rule\Condition
 */
class Product extends AbstractProduct
{
    /**
     * Validate product attribute value for condition
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $attrCode = $this->getAttribute();
        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($model->getAvailableInCategories());
        }

        $oldAttrValue = $model->getData($attrCode);
        if ($oldAttrValue === null) {
            return false;
        }

        $this->_setAttributeValue($model);

        $result = $this->validateAttribute($model->getData($attrCode));
        $this->_restoreOldAttrValue($model, $oldAttrValue);

        return (bool)$result;
    }

    /**
     * Restore old attribute value
     * @param \Magento\Framework\Model\AbstractModel $model
     * @param $oldAttrValue
     */
    protected function _restoreOldAttrValue(\Magento\Framework\Model\AbstractModel $model, $oldAttrValue)
    {
        $attrCode = $this->getAttribute();
        if ($oldAttrValue === null) {
            $model->unsetData($attrCode);
        } else {
            $model->setData($attrCode, $oldAttrValue);
        }
    }

    /**
     * Set attribute value
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return $this
     */
    protected function _setAttributeValue(\Magento\Framework\Model\AbstractModel $model)
    {
        $storeId        = $model->getStoreId();
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        if (!isset($this->_entityAttributeValues[$model->getId()])) {
            return $this;
        }

        $productValues = $this->_entityAttributeValues[$model->getId()];

        if (!isset($productValues[$storeId]) && !isset($productValues[$defaultStoreId])) {
            return $this;
        }

        $value = isset($productValues[$storeId]) ? $productValues[$storeId] : $productValues[$defaultStoreId];

        $value = $this->_prepareDatetimeValue($value, $model);
        $value = $this->_prepareMultiselectValue($value, $model);

        $model->setData($this->getAttribute(), $value);

        return $this;
    }

    /**
     * Prepare datetime attribute value
     *
     * @param mixed $value
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Model\AbstractModel $model
     * @return mixed
     */
    protected function _prepareDatetimeValue($value, \Magento\Framework\Model\AbstractModel $model)
    {
        $attribute = $model->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getBackendType() == 'datetime') {
            $value = strtotime($value);
        }

        return $value;
    }

    /**
     * Prepare multiselect attribute value
     *
     * @param mixed $value
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Model\AbstractModel $model
     * @return mixed
     */
    protected function _prepareMultiselectValue($value, \Magento\Framework\Model\AbstractModel $model)
    {
        $attribute = $model->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getFrontendInput() == 'multiselect') {
            $value = strlen($value) ? explode(',', $value) : [];
        }

        return $value;
    }
}
