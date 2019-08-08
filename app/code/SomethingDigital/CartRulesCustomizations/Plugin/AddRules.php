<?php

namespace SomethingDigital\CartRulesCustomizations\Plugin;

use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\SalesRule\Model\RulesApplier;
use Magento\SalesRule\Model\Rule;
use Magento\Framework\Registry;
use SomethingDigital\CartRulesCustomizations\Model\FreeGiftSku;
use Magento\Framework\Session\SessionManagerInterface;

class AddRules
{
    protected $registry;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        Rule $rule,
        Registry $registry,
        FreeGiftSku $freeGiftSku,
        SessionManagerInterface $session
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->ruleModel = $rule;
        $this->registry = $registry;
        $this->freeGiftSku = $freeGiftSku;
        $this->session = $session;
    }

    public function beforeApplyRules(RulesApplier $subject, $item, $rules, $skipValidation, $couponCode)
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
            $collection->setOrder('sort_order', Collection::SORT_ORDER_ASC);
            $rules = $collection->load(); 

            return [$item, $rules, $skipValidation, $couponCode];
        }
    }

    public function afterApplyRules(RulesApplier $subject, $result)
    {
        $freeGiftSkus = $this->freeGiftSku->skus;
        $skuSuffix = '';
        foreach ($result as $ruleId) {
            $rule = $this->ruleModel->load($ruleId);
            $giftSku = $rule->getFreeGiftSku();
            if (!in_array($giftSku, $freeGiftSkus) && !empty($giftSku)) {
                $freeGiftSkus[] = $giftSku;
            }

            if (!empty($rule->getSkuSuffix())) {
                $skuSuffix = $rule->getSkuSuffix();
            }

        }
        $this->freeGiftSku->skus = $freeGiftSkus;

        if ($skuSuffix) {
            $this->session->setSkuSuffix($skuSuffix);
        }

        return $result;
    }
}