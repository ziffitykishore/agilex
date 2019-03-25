<?php

namespace SomethingDigital\CartRulesCustomizations\Plugin;

use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Quote\ChildrenValidationLocator;
use Magento\Framework\App\ObjectManager;

class AddRules{

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    public function beforeApplyRules (\Magento\SalesRule\Model\RulesApplier $subject, $item, $rules, $skipValidation, $couponCode)
    {
        if ($couponCode) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            if ($this->customerSession->isLoggedIn()) {
                $customerGroupId = $this->customerSession->getCustomer()->getGroupId();
            } else {
                $customerGroupId = 0;
            }
            $collection = $this->collectionFactory->create();

            $collection->getSelect()->joinLeft(
                ['salesrule_coupon' => $collection->getTable('salesrule_coupon')],
                'main_table.rule_id = salesrule_coupon.rule_id',
                ['code']
            );
            $collection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
            $collection->addFieldToFilter('salesrule_coupon.code', [
                ['like' => $couponCode.':%'],
                ['eq' => $couponCode]
            ]);
            $rules = $collection->load(); 

            return [$item, $rules, $skipValidation, $couponCode];
        }
    }
}