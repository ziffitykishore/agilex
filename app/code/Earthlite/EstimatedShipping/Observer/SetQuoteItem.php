<?php
declare(strict_types = 1);
namespace Earthlite\EstimatedShipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Earthlite\EstimatedShipping\Model\Shipping\Estimation;

/**
 * class SetQuoteItem
 */
class SetQuoteItem implements ObserverInterface
{
    /**
     *
     * @var Estimation 
     */
   protected $shippingEstimation;
   
   /**
    * 
    * @param Estimation $shippingEstimation
    */
    public function __construct(                
        Estimation $shippingEstimation
    ) {          
        $this->shippingEstimation = $shippingEstimation;
    }
    
    /**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {                
        $quoteItem = $observer->getQuoteItem();
        if ($quoteItem->getChildren()) {
            $shippingInfoSku = $quoteItem->getChildren()[0]->getSku();
        } else {
            $shippingInfoSku = $quoteItem->getSku();
        }
        $quoteItem->setShippingLeadTime($this->getShippingInfo($shippingInfoSku));        
        $quoteItem->setItemType($this->getItemType($shippingInfoSku));        
    }

    /**
     * 
     * @param type $sku
     * @return string
     */
    public function getShippingInfo($sku)
    {
        return $this->shippingEstimation->getQuoteEstimatedShipping($sku);   
    }
    
    /**
     * 
     * @param string $sku
     */
    public function getItemType(string $sku):bool
    {
        return (bool) $this->shippingEstimation->getItemType($sku);
    }
}