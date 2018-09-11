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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Rule\Model\Condition;
use Magento\Rule\Model\Condition\Context;
use Mageplaza\SeoRule\Model\Rule\Condition\ProductFactory;

/**
 * Class Combine
 * @package Mageplaza\SeoRule\Model\Rule\Condition
 */
class Combine extends Condition\Combine
{
    /**
     * @var \Mageplaza\SeoRule\Model\Rule\Condition\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Combine constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Mageplaza\SeoRule\Model\Rule\Condition\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\State $state
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        Registry $registry,
        RequestInterface $request,
        State $state,
        array $data = []
    )
    {
        $this->productFactory = $productFactory;
        $this->registry       = $registry;
        $this->request        = $request;
        $this->state          = $state;

        parent::__construct($context, $data);

        $this->setType('Mageplaza\SeoRule\Model\Rule\Condition\Combine');
    }

    /**
     * Get new child select options
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $type              = $this->request->getParam('type');
        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();

        $attributes        = [];
        $parentOptionLabel = 'Product';

        $conditions = parent::getNewChildSelectOptions();
        foreach ($productAttributes as $code => $label) {
            if ($type == \Mageplaza\SeoRule\Model\Rule\Source\Type::PRODUCTS) {
                if ($code == 'activity') {
                    continue;
                }
            } else if ($type == \Mageplaza\SeoRule\Model\Rule\Source\Type::LAYERED_NAVIGATION) {
                $parentOptionLabel = 'Layered Navigation';
                if (in_array($code, ['attribute_set_id', 'sku', 'category_ids'])) {
                    continue;
                }
            }

            $attributes[] = [
                'value' => 'Mageplaza\SeoRule\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        if ($type == \Mageplaza\SeoRule\Model\Rule\Source\Type::LAYERED_NAVIGATION) {
            $conditions = array_merge_recursive($conditions, [
                    ['label' => __($parentOptionLabel), 'value' => $attributes]
                ]
            );
        } else {
            $conditions = array_merge_recursive($conditions, [
                    [
                        'value' => 'Mageplaza\SeoRule\Model\Rule\Condition\Combine',
                        'label' => __('Conditions Combination'),
                    ],
                    ['label' => __($parentOptionLabel), 'value' => $attributes]
                ]
            );
        }

        return $conditions;
    }

    /**
     * Collect validate attributes
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    /**
     * @param int|\Magento\Framework\Model\AbstractModel $entity
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _isValid($entity)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all  = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            if ($entity instanceof \Magento\Framework\Model\AbstractModel) {
                $validated = $cond->validate($entity);
            } else {
                $validated = $cond->validateByEntityId($entity);
            }

            if ($all && $validated !== $true) {
                return false;
            } else if (!$all && $validated === $true) {
                return true;
            }
        }
        $result = $all ? true : false;

        if ($this->state->getAreaCode() == 'frontend' && count($this->request->getParams()) > 1) {
            $request = $this->request->getParams();
            unset($request['id']);
            if ($result) {
                if ($all && $true) {
                    if (count($request) > count($this->getConditions())) {
                        $result = false;
                    }
                }
            } else {
                if (!$all && !$true) {
                    if (count($request) > count($this->getConditions())) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
}
