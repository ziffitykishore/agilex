<?php

namespace Ziffity\Pickupdate\Plugin\Order;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Ziffity\Pickupdate\Model\LoaderExtensions;
use Magento\Sales\Model\Order;

class OrderSenderPlugin
{
    /**
     * @var LoaderExtensions
     */
    private $loaderExtensions;

    public function __construct(
        LoaderExtensions $loaderExtensions
    ) {
        $this->loaderExtensions = $loaderExtensions;
    }

    /**
     * @param OrderSender $subject
     * @param Order $order
     * @param bool $forceSyncMode
     *
     * @return array
     */
    public function beforeSend(OrderSender $subject, Order $order, $forceSyncMode = false)
    {
        $this->loaderExtensions->loadPickupDateExtensionAttributes($order);

        return [$order, $forceSyncMode];
    }
}
