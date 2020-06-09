<?php
namespace Earthlite\EstimatedShipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product;
use Earthlite\EstimatedShipping\Model\Shipping\Estimation;

class SetQuoteItem implements ObserverInterface
{
   protected $product;

   protected $quote;

    public function __construct(                
        \Magento\Quote\Model\Quote\Item $quote,
        Estimation $shippingEstimation
    ) {          
        $this->quote = $quote;
        $this->shippingEstimation = $shippingEstimation;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {                
        $quoteItem = $observer->getQuoteItem();
        $itemSku = $quoteItem->getSku();        
        $leadTime = $this->getShippingInfo($itemSku);        
        $quoteItem->setShippingLeadTime($leadTime);        
        
        return $observer;
    }

    public function getShippingInfo($sku)
    {
        return $this->shippingEstimation->getQuoteEstimatedShipping($sku);   
    }
}