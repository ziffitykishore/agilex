<?php
 
namespace Ziffity\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Ziffity\Checkout\Model\Data\OrderInfo;

class AddOrderInfoToOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        /** @var $order \Magento\Sales\Model\Order **/

        $quote = $observer->getEvent()->getQuote();
        /** @var $quote \Magento\Quote\Model\Quote **/

        $order->setData(
            OrderInfo::STORE_LOCATION,
            $quote->getData(OrderInfo::STORE_LOCATION)
        );

        $order->setData(
            OrderInfo::STORE_ADDRESS,
            $quote->getData(OrderInfo::STORE_ADDRESS)
        );
    }
}
