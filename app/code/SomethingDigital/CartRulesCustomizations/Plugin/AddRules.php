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
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;

class AddRules
{
    protected $registry;
    protected $collectionFactory;
    protected $storeManager;
    protected $customerSession;
    protected $ruleModel;
    protected $freeGiftSku;
    protected $session;
    protected $quote;
    protected $cart;
    protected $quoteRepository;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        Rule $rule,
        Registry $registry,
        FreeGiftSku $freeGiftSku,
        SessionManagerInterface $session,
        Quote $quote,
        Cart $cart,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->ruleModel = $rule;
        $this->registry = $registry;
        $this->freeGiftSku = $freeGiftSku;
        $this->session = $session;
        $this->quote = $quote;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
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
            // $skuSuffix from the cart rule can contain symbols like "#TP3"
            $this->session->setSkuSuffix($skuSuffix);
            $this->quote->repriceCustomerQuote();

            $currentQuote = $this->cart->getQuote();
            if ($currentQuote && $currentQuote->getId()) {
                $quote = $this->quoteRepository->get($currentQuote->getId());
                $quote->setSuffix($skuSuffix);
                $this->quoteRepository->save($quote);
            }
        }

        return $result;
    }
}