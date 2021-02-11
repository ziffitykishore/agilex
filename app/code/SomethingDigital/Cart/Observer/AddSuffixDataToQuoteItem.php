<?php

namespace SomethingDigital\Cart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

class AddSuffixDataToQuoteItem implements ObserverInterface
{
    /**
     * Execute observer method.
     *
     * @param Observer $observer Observer
     *
     * @return void
     */

    protected $request;
    protected $session;
    protected $productCollection;
    protected $config;
    protected $url;

    public function __construct( 
        Http $request,
        SessionManagerInterface $session,
        Collection $productCollection,
        ScopeConfigInterface $config,
        UrlInterface $url
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->productCollection = $productCollection;
        $this->config = $config;
        $this->_urlsession = $url;
    }

    public function execute(Observer $observer)
    {
        $item   = $observer->getEvent()->getData('quote_item');
        $item   = ($item->getParentItem() ? $item->getParentItem() : $item);
        //$suffix =   '';

        if (!empty($this->session->getSkuSuffix())) {
            $suffix = json_decode($this->session->getSkuSuffix());
            if (!empty($suffix)) {
                foreach ($suffix as $key => $code) {
                    if(!empty($code)) {
                        $skuValue = explode('~', $code);
                        if($item->getProduct()->getSku() == $skuValue[1]) {
                            $item->setData(
                                'suffix',
                                $skuValue[0]
                            );
                        }
                    }
                }
            }
        }
    }

    // private function getSuffixCode() 
    // {
    //     $url = $this->_urlsession->getCurrentUrl();
    //     // $url = parse_url($url);
    //     // $queryText = $url["fragment"];
    //     $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/200.log');
    //     $logger = new \Zend\Log\Logger();
    //     $logger->addWriter($writer);
    //     $logger->info($url);
    //     exit;
    //     // $minSkuLength = $this->config->getValue('catalog/search/min_sku_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    //     // $maxSuffixLength = $this->config->getValue('catalog/search/max_suffix_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    //     // $suffixMatch = substr($queryText, 0, max($minSkuLength, strlen($queryText) - $maxSuffixLength));

        
        
    //     // $productcollection = $this->productCollection
    //     //         ->setFlag('has_stock_status_filter', false)
    //     //         ->addAttributeToSelect(['sku'])
    //     //         ->addAttributeToFilter('sku', array('like' => $suffixMatch.'%'));

    //     // $suffixCodes = [];
    //     // foreach ($productcollection as $key => $product) {
    //     //     $sku = $product->getSku();
    //     //     if ($queryText == $sku) {
    //     //         return false;
    //     //     }

    //     //     if (strpos($queryText, $sku) === 0) {
    //     //         $skuSuffix = substr($queryText, strlen($sku));
    //     //         $suffixHasSymbols = strcspn($skuSuffix, '~!@#$%^&*()=+-_?:<>[]{}') !== strlen($skuSuffix);

    //     //         if (!$suffixHasSymbols && $skuSuffix) {
    //     //             $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/200.log');
    //     //             $logger = new \Zend\Log\Logger();
    //     //             $logger->addWriter($writer);
    //     //             $logger->info($skuSuffix);
    //     //             exit;
    //     //             //$suffixCodes[] = $skuSuffix;
    //     //         }
    //     //     }
    //     // }

    //     // return $suffixCodes;
    // }
}
