<?php

namespace SomethingDigital\SearchCustomization\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;

class Suffix
{

    protected $request;
    protected $session;
    protected $productCollection;
    protected $config;
    private $quote;

    public function __construct(
        Http $request,
        SessionManagerInterface $session,
        Collection $productCollection,
        ScopeConfigInterface $config,
        Quote $quote
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->productCollection = $productCollection;
        $this->config = $config;
        $this->quote = $quote;
    }

    public function beforeExecute(\Magento\CatalogSearch\Controller\Result\Index $subject)
    {
        $queryText = $this->request->getParam('q');

        $minSkuLength = $this->config->getValue('catalog/search/min_sku_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $maxSuffixLength = $this->config->getValue('catalog/search/max_suffix_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $suffixMatch = substr($queryText, 0, max($minSkuLength, strlen($queryText) - $maxSuffixLength));

        $productcollection = $this->productCollection
                ->addAttributeToSelect(['sku'])
                ->addAttributeToFilter('sku', array('like' => $suffixMatch.'%'));

        foreach ($productcollection as $key => $product) {
            $sku = $product->getSku();
            if ($queryText == $sku) {
                $subject->getResponse()->setRedirect($product->getProductUrl());
            }

            if (strpos($queryText, $sku) === 0) {
                $skuSuffix = substr($queryText, strlen($sku));
                $this->session->setSkuSuffix($skuSuffix);
                $this->quote->repriceCustomerQuote();
                $subject->getResponse()->setRedirect($product->getProductUrl());
            }
        }

    }
}
