<?php
namespace Ziffity\Promo\Observer;

class UpdateFreeItems implements \Magento\Framework\Event\ObserverInterface
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
      try{
            $info = $observer->getInfo();
            $quote = $observer->getCart()->getQuote();
            foreach ($info as $itemId => $item) {
                $item = $quote->getItemById($itemId);
                if (!$item)
                    continue;
                if (!$item->getOptionByCode('ampromo_rule'))
                    continue;
                if (empty($info[$itemId]))
                    continue;
                $info[$itemId]['remove'] = true;
            }
      } catch (Exception $ex) {
      }
    return $this;
  }
}