<?php

namespace Ziffity\Deliverydate\Plugin\Order;

use Amasty\Deliverydate\Plugin\Order\OrderSave as OrderSavePlugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSave extends OrderSavePlugin
{

    /**
     * @var \Amasty\Deliverydate\Helper\Data
     */
    private $amHelper;

    /**
     * @var \Amasty\Deliverydate\Model\DeliverydateFactory
     */
    private $deliverydateFactory;

    /**
     * @var \Amasty\Deliverydate\Model\ResourceModel\Deliverydate
     */
    private $deliverydateResource;

    public function __construct(
        \Amasty\Deliverydate\Helper\Data $amHelper,
        \Amasty\Deliverydate\Model\DeliverydateFactory $deliverydateFactory,
        \Amasty\Deliverydate\Model\ResourceModel\Deliverydate $deliverydateResource
    ) {
        $this->amHelper = $amHelper;
        $this->deliverydateFactory = $deliverydateFactory;
        $this->deliverydateResource = $deliverydateResource;
    }
    
    
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        
        
        $data = $this->amHelper->getDeliveryDataFromSession();
        if (is_array($data)) {
            /** @var \Amasty\Deliverydate\Model\Deliverydate $deliveryDate */
            $deliveryDate = $this->deliverydateFactory->create();
             if ($deliveryDate->isDelivery()) {
                $deliveryDate->prepareForSave($data, $order);
                $deliveryDate->validateDelivery($data, $order);
            }
        }

        return [$order];
    }

    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        
        $data = $this->amHelper->getDeliveryDataFromSession();
        if (is_array($data)) {
            /** @var \Amasty\Deliverydate\Model\Deliverydate $deliveryDate */
            $deliveryDate = $this->deliverydateFactory->create();
            if ($deliveryDate->isDelivery()) {
                if ($deliveryDate->prepareForSave($data, $order)) {
                    $this->deliverydateResource->save($deliveryDate);
                }
            }
        }
        return $order;
    }    
}