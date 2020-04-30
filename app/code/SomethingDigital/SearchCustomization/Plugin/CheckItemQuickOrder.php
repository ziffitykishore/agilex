<?php

namespace SomethingDigital\SearchCustomization\Plugin;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart;

class CheckItemQuickOrder
{
    protected $session;
    protected $productCollection;
    protected $config;
    private $quote;
    protected $cart;
    protected $quoteRepository;

    public function __construct(
        SessionManagerInterface $session,
        Collection $productCollection,
        ScopeConfigInterface $config,
        Quote $quote,
        Cart $cart,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->session = $session;
        $this->productCollection = $productCollection;
        $this->config = $config;
        $this->quote = $quote;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeCheckItem(\Magento\QuickOrder\Model\Cart $subject, $sku, $qty, $config)
    {
        $queryText = $sku;

        $minSkuLength = $this->config->getValue('catalog/search/min_sku_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $maxSuffixLength = $this->config->getValue('catalog/search/max_suffix_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $suffixMatch = substr($queryText, 0, max($minSkuLength, strlen($queryText) - $maxSuffixLength));

        $productcollection = $this->productCollection
                ->addAttributeToSelect(['sku'])
                ->addAttributeToFilter('sku', array('like' => $suffixMatch.'%'));

        foreach ($productcollection as $key => $product) {
            if (stripos($queryText, $product->getSku()) === 0) {
                $sku = $product->getSku();
                $skuSuffix = substr($queryText, strlen($sku));
                $this->session->setSkuSuffix($skuSuffix);
            }
        } 
        return [$sku, $qty, $config];
    }

}