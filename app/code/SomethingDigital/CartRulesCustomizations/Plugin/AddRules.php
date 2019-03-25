<?php

namespace SomethingDigital\CartRulesCustomizations\Plugin;

use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Quote\ChildrenValidationLocator;
use Magento\Framework\App\ObjectManager;

class AddRules{

    /**
     * Rule source collection
     *
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    protected $_rules;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function beforeApplyRules (\Magento\SalesRule\Model\RulesApplier $subject, $item, $rules, $skipValidation, $couponCode)
    {
        if ($couponCode) {
            $collection = $this->_collectionFactory->create();

            $collection->getSelect()->joinLeft(
                ['salesrule_coupon' => $collection->getTable('salesrule_coupon')],
                'main_table.rule_id = salesrule_coupon.rule_id',
                ['code']
            );
            $collection->addFieldToFilter('salesrule_coupon.code', array(
                array('like' => $couponCode.'%'),
            ));
            $rules = $collection->load(); 

            return [$item, $rules, $skipValidation, $couponCode];
        }
    }
}