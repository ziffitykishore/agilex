<?php

namespace Ziffity\Pickupdate\Observer\Admin\Order;

use Ziffity\Pickupdate\Model\ResourceModel\Pickupdate;
use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface
{

    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateFactory
     */
    private $pickupdateFactory;

    /**
     * @var \Ziffity\Pickupdate\Model\TintervalFactory
     */
    private $tintervalFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Pickupdate
     */
    private $pickupdateResourceModel;

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Tinterval
     */
    private $tintervalResourceModel;

    /**
     * @var Pickupdate
     */
    private $pickupdateResource;

    public function __construct(
        \Ziffity\Pickupdate\Model\PickupdateFactory $pickupdateFactory,
        \Ziffity\Pickupdate\Model\TintervalFactory $tintervalFactory,
        \Magento\Framework\App\RequestInterface $request,
        Pickupdate $pickupdateResourceModel,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval $tintervalResourceModel,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResource
    ) {
        $this->pickupdateFactory = $pickupdateFactory;
        $this->tintervalFactory = $tintervalFactory;
        $this->request = $request;
        $this->pickupdateResourceModel = $pickupdateResourceModel;
        $this->tintervalResourceModel = $tintervalResourceModel;
        $this->pickupdateResource = $pickupdateResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();

        $data = $this->request->getParam('pickupdate');
        if (is_array($data) && !empty($data)) {
            /** @var \Ziffity\Pickupdate\Model\PickupDate $pickupDate */
            $pickupDate = $this->pickupdateFactory->create();
            if ($pickupDate->prepareForSave($data, $order)) {
                $this->pickupdateResource->save($pickupDate);
            }
        }
    }
}
