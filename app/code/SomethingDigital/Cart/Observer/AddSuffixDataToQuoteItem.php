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
}
