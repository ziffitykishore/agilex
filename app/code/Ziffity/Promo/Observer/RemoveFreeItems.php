<?php
namespace Ziffity\Promo\Observer;

class RemoveFreeItems implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;
    protected $coreHelper;

    public function __construct(
        \Ziffity\Core\Helper\Data $coreHelper,
        \Ziffity\Promo\Helper\Data $Helper
            
    ){
        $this->coreHelper = $coreHelper;
        $this->helper = $Helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try{
            $checkoutSession = $this->helper->getCheckoutSession();
            $item = $observer->getEvent()->getQuoteItem();
            if ($item->getId() != $this->coreHelper->getRegister()->registry('ampromo_del')){
                $allowDelete = $this->coreHelper->getScopeConfig('ampromo/general/allow_delete');    
                if ($allowDelete){
                    $arr = $checkoutSession->getAmpromoDeletedItems();
                    if (!is_array($arr)){
                        $arr = array();
                    }
                    $arr[$item->getSku()] = true;
                    $checkoutSession->setAmpromoDeletedItems($arr);
                    $checkoutSession->setAmpromoId($item->getQuote()->getId());
                }
            }
        } catch (Exception $ex) {

        }
        return $this;
    }
}