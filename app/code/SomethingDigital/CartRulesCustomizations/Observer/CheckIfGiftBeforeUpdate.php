<?php

namespace SomethingDigital\CartRulesCustomizations\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckIfGiftBeforeUpdate implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $info = $observer->getEvent()->getInfo();
    
        foreach ($info->getData() as $itemId => $itemInfo) {
            $item = $cart->getQuote()->getItemById($itemId);
            $options = $item->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() == 'free_gift' && $option->getValue() == 1) {
                       if ($item->getQty() != $itemInfo['qty']) {
                            throw new \Magento\Framework\Exception\LocalizedException(__("Free Gift quantity can not be changed."));
                       }
                    }
                }
            }
        }
    }
}