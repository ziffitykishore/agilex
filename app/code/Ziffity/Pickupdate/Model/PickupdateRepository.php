<?php

namespace Ziffity\Pickupdate\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class PickupdateRepository
{
    /**
     * @var ResourceModel\Pickupdate
     */
    private $pickupdateResource;

    /**
     * @var PickupdateFactory
     */
    private $pickupdateFactory;

    /**
     * @var array
     */
    protected $modelsByOrder = [];

    /**
     * PickupdateRepository constructor.
     *
     * @param ResourceModel\Pickupdate $pickupdateResource
     * @param PickupdateFactory        $pickupdateFactory
     */
    public function __construct(
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResource,
        \Ziffity\Pickupdate\Model\PickupdateFactory $pickupdateFactory
    ) {
        $this->pickupdateResource = $pickupdateResource;
        $this->pickupdateFactory = $pickupdateFactory;
    }

    /**
     * @param int $orderId
     *
     * @return Pickupdate
     * @throws NoSuchEntityException
     */
    public function getByOrder($orderId)
    {
        if (!isset($this->modelsByOrder[$orderId])) {
            /** @var Pickupdate $model */
            $model = $this->pickupdateFactory->create();
            $this->pickupdateResource->load($model, $orderId, 'order_id');
            if (!$model->getId()) {
                throw new NoSuchEntityException(__('Pickup Date for specified order ID "%1" not found.', $orderId));
            }
            $this->modelsByOrder[$orderId] = $model;
        }
        return $this->modelsByOrder[$orderId];
    }

    /**
     * @param Pickupdate $pickupdate
     *
     * @return Pickupdate
     */
    public function save($pickupdate)
    {
        $this->pickupdateResource->save($pickupdate);
        return $pickupdate;
    }
}
