<?php

namespace Ziffity\Pickupdate\Plugin\Order;

use Ziffity\Pickupdate\Model\LoaderExtensions;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * @since @1.4.0
 */
class OrderRepositoryPlugin
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
     * @param OrderRepository   $subject
     * @param OrderInterface    $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepository $subject, OrderInterface $order)
    {
        $this->loaderExtensions->loadPickupDateExtensionAttributes($order);

        return $order;
    }

    /**
     * @param OrderRepository               $subject
     * @param OrderSearchResultInterface    $orderCollection
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepository $subject, OrderSearchResultInterface $orderCollection)
    {
        foreach ($orderCollection->getItems() as $order) {
            $this->loaderExtensions->loadPickupDateExtensionAttributes($order);
        }

        return $orderCollection;
    }
}
