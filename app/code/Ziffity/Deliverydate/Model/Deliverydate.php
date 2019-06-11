<?php

namespace Ziffity\Deliverydate\Model;

use Amasty\Deliverydate\Model\Deliverydate as AmastyDeliveryDate;
use Amasty\Deliverydate\Helper\Data as DeliverydateHelper;

class Deliverydate extends AmastyDeliveryDate
{
    /**
     * @var ResourceModel\Tinterval
     */
    protected $tintervalResourceModel;

    /**
     * @var TintervalFactory
     */
    protected $tintervalFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Amasty\Deliverydate\Helper\Data
     */
    protected $deliveryHelper;

    /**
     * @var \Magento\Framework\Data\Form\Filter\DateFactory
     */
    private $dateFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Amasty\Deliverydate\Model\DeliverydateConfigProvider
     */
    private $configProvider;
    /**
     * @var DeliveryDate\Validator
     */
    private $dateValidator;

    /** @var \Magento\Framework\Message\ManagerInterface */
    protected $messageManager;

    protected $helper;

    protected $pickupHelper;

    protected function _construct()
    {
        parent::_construct();
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Deliverydate\Model\ResourceModel\Deliverydate $resource,
        \Amasty\Deliverydate\Model\ResourceModel\Deliverydate\Collection $resourceCollection,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Amasty\Deliverydate\Model\TintervalFactory $tintervalFactory,
        \Amasty\Deliverydate\Model\ResourceModel\Tinterval $tintervalResourceModel,
        \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory,
        \Amasty\Deliverydate\Helper\Data $deliveryHelper,
        \Amasty\Deliverydate\Model\DeliverydateConfigProvider $configProvider,
        \Amasty\Deliverydate\Model\DeliveryDate\Validator $dateValidator,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        DeliverydateHelper $helper,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $orderRepository, 
                $tintervalFactory, $tintervalResourceModel, $dateFactory, $deliveryHelper, 
                $configProvider, $dateValidator, $messageManager, $data);
        
        $this->dateValidator          = $dateValidator;
        $this->helper                 = $helper;
        $this->pickupHelper           = $pickupHelper;
    }
    
    public function validateDelivery($data, $order)
    {
                
        $shippingMethod = $order->getShippingMethod();
        $customerGroup = $order->getCustomerGroupId();
        $pickupData = $this->pickupHelper->getPickupDataFromSession();
        if(!$pickupData['date'] && !$pickupData['tinterval_id']) {
            if ($data['tinterval_id']) {
            /* load Time interval by ID and combine it to string */
            $tint = $this->tintervalFactory->create();
            $this->tintervalResourceModel->load($tint, (int)$data['tinterval_id']);
            $data['time'] = $tint->getTimeFrom() . " - " . $tint->getTimeTo();
        }


        if (!$this->getDate()
            && $this->deliveryHelper->isFieldEnabled('date', $shippingMethod, $customerGroup)
            && $this->deliveryHelper->getStoreScopeValue('date_field/required')
        ) {
            $this->throwValidatorException(__('Delivery Date is required'));
        }

        if (!$this->getTintervalId()
            && $this->deliveryHelper->isFieldEnabled('time', $shippingMethod, $customerGroup)
            && $this->deliveryHelper->getStoreScopeValue('time_field/required')
        ) {
            $this->throwValidatorException(__('Delivery Time is required'));
        }

        if (!$this->getComment()
            && $this->deliveryHelper->getStoreScopeValue('comment_field/required')
            && $this->deliveryHelper->isFieldEnabled('comment', $shippingMethod, $customerGroup)
        ) {
            $this->throwValidatorException(__('Delivery Comment is required'));
        }


        switch ($this->helper->getWebsiteScopeValue('quota/quota_type')) {
            case 'day':
                if ($this->getDate() && !$this->dateValidator->validate($this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Delivery Date is invalid, please choose another date'));
                }
                break;
            case 'week_day':
                if ($this->getDate() && !$this->dateValidator->validate($this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Delivery Date is invalid, please choose another date'));
                }
                break;
            case 'time_slot':
                if ($this->getDate() && !$this->dateValidator->validateTimeSlot($data['time'], $this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Delivery Time Slot is invalid, please choose another Time Slot'));
                }
                break;
        }


        if ($this->getDate() && !$this->dateValidator->validateTimeSlot($data['time'], $this->getDate(), $this->getTimeWithInterval())) {
            $this->throwValidatorException(__('Delivery Date is invalid, please choose another date'));
        }

        if ($this->getDate()
            && $this->getTintervalId()
            && $this->dateValidator->disablePastTime()
        ) {
            $this->throwValidatorException(__('Delivery Time is invalid, please choose another time'));
        }
    }
    }
    

    private function throwValidatorException($message)
    {
        $this->messageManager->addErrorMessage($message);
        throw new \Magento\Framework\Exception\ValidatorException($message);
    }


    private function getTimeWithInterval()
    {
        $date = $this->getDate();
        $timestamp = strtotime($date);
        $time = $this->getTime();
        if ($time) {
            preg_match_all('/\d+:\d+/', $time, $timeIntervals);
            $endInterval = $timeIntervals[0][1];
            preg_match_all('/\d+/', $endInterval, $times);
            $hours = $times[0][0];
            $minutes = $times[0][1];
            $timestamp = $hours * 60 * 60 + $minutes * 60 + $timestamp;
        }

        return $timestamp;
    }
    
}
