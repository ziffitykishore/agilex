<?php

namespace SomethingDigital\SearchCustomization\Plugin;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart;

class UploadFileQuickOrder
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
        CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->session = $session;
        $this->productCollection = $productCollection;
        $this->config = $config;
        $this->quote = $quote;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
    }

    public function beforeExecute(\Magento\QuickOrder\Controller\Sku\UploadFile $subject)
    {
        $items = $this->request->getPost('items');
        $minSkuLength = $this->config->getValue('catalog/search/min_sku_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $maxSuffixLength = $this->config->getValue('catalog/search/max_suffix_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $finalItems = [];
        foreach ($items as $key => $item) {
            if (!$item['sku']) {
                continue;
            }
            $suffixMatch = substr($item['sku'], 0, max($minSkuLength, strlen($item['sku']) - $maxSuffixLength));

            $this->productCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
            $productcollection = $this->productCollection
                ->addAttributeToSelect(['sku'])
                ->addAttributeToFilter('sku', array('like' => $suffixMatch.'%'));

            foreach ($productcollection as $product) {
                if (strpos($item['sku'], $product->getSku()) === 0) {
                    $finalItems[] = [
                        'sku' => $product->getSku(),
                        'qty' => $item['qty']
                    ];
                    $skuSuffix = substr($item['sku'], strlen($product->getSku()));
                    if ($skuSuffix != '') {
                        $this->session->setSkuSuffix($skuSuffix);

                        $currentQuote = $this->cart->getQuote();
                        if ($currentQuote->getId()) {
                            $quote = $this->quoteRepository->get($currentQuote->getId());
                            $quote->setSuffix($skuSuffix);
                        }
                    }
                }
            }

        }
        if ($skuSuffix) {
            $this->quote->repriceCustomerQuote($skuSuffix);
        }
        $this->request->setPostValue('items',$finalItems);
    }

}