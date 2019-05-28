<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */


namespace Ziffity\Cart\Block\Checkout;

use Amasty\Deliverydate\Helper\Data;
use Amasty\Deliverydate\Model\DeliverydateConfigProvider;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class LayoutProcessorDisplay implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $deliveryHelper;

    /**
     * @var \Amasty\Deliverydate\Model\ResourceModel\Tinterval\Collection
     */
    protected $tintervalCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Amasty\Deliverydate\Model\DeliveryDate
     */
    protected $deliveryDate;

    /**
     * @var DeliverydateConfigProvider
     */
    private $configProvider;

    /**
     * @var \Amasty\Deliverydate\Model\Tinterval
     */
    protected $tintervalModel;

    public function __construct(
        Data $deliveryHelper,
        \Amasty\Deliverydate\Model\ResourceModel\Tinterval\Collection $tintervalCollection,
        \Magento\Customer\Model\Session $customerSession,
        DeliverydateConfigProvider $configProvider,
        \Amasty\Deliverydate\Model\Deliverydate $deliveryDate,
        \Amasty\Deliverydate\Model\Tinterval $tintervalModel
    ) {
        $this->deliveryHelper = $deliveryHelper;
        $this->tintervalCollection = $tintervalCollection;
        $this->customerSession = $customerSession;
        $this->configProvider = $configProvider;
        $this->deliveryDate = $deliveryDate;
        $this->tintervalModel = $tintervalModel;
    }

    /**
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        return $jsLayout;
    }

}
