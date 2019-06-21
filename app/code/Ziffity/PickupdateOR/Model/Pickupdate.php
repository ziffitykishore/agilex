<?php

namespace Ziffity\PickupdateOR\Model;

use Ziffity\Pickupdate\Model\Pickupdate as ZiffityPickupDate;
use Ziffity\Pickupdate\Helper\Data as PickupdateHelper;

class Pickupdate extends ZiffityPickupDate
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
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;

    /**
     * @var \Magento\Framework\Data\Form\Filter\DateFactory
     */
    private $dateFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateConfigProvider
     */
    private $configProvider;
    /**
     * @var PickupDate\Validator
     */
    private $dateValidator;

    /** @var \Magento\Framework\Message\ManagerInterface */
    protected $messageManager;

    protected $helper;

    protected $cookieManager;

    protected function _construct()
    {
        parent::_construct();
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $resource,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection $resourceCollection,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Ziffity\Pickupdate\Model\TintervalFactory $tintervalFactory,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval $tintervalResourceModel,
        \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Ziffity\Pickupdate\Model\PickupdateConfigProvider $configProvider,
        \Ziffity\Pickupdate\Model\PickupDate\Validator $dateValidator,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PickupdateHelper $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $orderRepository, 
                $tintervalFactory, $tintervalResourceModel, $dateFactory, $pickupHelper, 
                $configProvider, $dateValidator, $messageManager, $data);
        
        $this->dateValidator          = $dateValidator;
        $this->helper                 = $helper;
        $this->cookieManager           = $cookieManager;
    }
    
    public function validatePickup($data, $order)
    {
        $shippingMethod = $order->getShippingMethod();
        $customerGroup = $order->getCustomerGroupId();
        if ($data['tinterval_id']) {
            /* load Time interval by ID and combine it to string */
            $tint = $this->tintervalFactory->create();
            $this->tintervalResourceModel->load($tint, (int)$data['tinterval_id']);
            $data['time'] = $tint->getTimeFrom() . " - " . $tint->getTimeTo();
        }

        if (!$this->getDate()
            && $this->pickupHelper->isFieldEnabled('date', $shippingMethod, $customerGroup)
            && $this->pickupHelper->getStoreScopeValue('date_field/required')
        ) {
            $this->throwValidatorException(__('Pickup Date is required'));
        }

        if (!$this->getTintervalId()
            && $this->pickupHelper->isFieldEnabled('time', $shippingMethod, $customerGroup)
            && $this->pickupHelper->getStoreScopeValue('time_field/required')
        ) {
            $this->throwValidatorException(__('Pickup Time is required'));
        }

        if (!$this->getComment()
            && $this->pickupHelper->getStoreScopeValue('comment_field/required')
            && $this->pickupHelper->isFieldEnabled('comment', $shippingMethod, $customerGroup)
        ) {
            $this->throwValidatorException(__('Pickup Comment is required'));
        }


        switch ($this->helper->getWebsiteScopeValue('quota/quota_type')) {
            case 'day':
                if ($this->getDate() && !$this->dateValidator->validate($this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Pickup Date is invalid, please choose another date'));
                }
                break;
            case 'week_day':
                if ($this->getDate() && !$this->dateValidator->validate($this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Pickup Date is invalid, please choose another date'));
                }
                break;
            case 'time_slot':
                if ($this->getDate() && !$this->dateValidator->validateTimeSlot($data['time'], $this->getDate(), $this->getTimeWithInterval())) {
                    $this->throwValidatorException(__('Pickup Time Slot is invalid, please choose another Time Slot'));
                }
                break;
        }


        if ($this->getDate() && !$this->dateValidator->validateTimeSlot($data['time'], $this->getDate(), $this->getTimeWithInterval())) {
            $this->throwValidatorException(__('Pickup Date is invalid, please choose another date'));
        }

        if ($this->getDate()
            && $this->getTintervalId()
            && $this->dateValidator->disablePastTime()
        ) {
            $this->throwValidatorException(__('Pickup Time is invalid, please choose another time'));
        }
    }
    
    public function isPickup()
    {
        return $this->cookieManager->getCookie('is_pickup') === 'true' ? true : false;
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
