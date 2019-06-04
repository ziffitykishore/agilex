<?php

namespace Ziffity\Pickupdate\Model;

use Ziffity\Pickupdate\Api\Data\PickupdateInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime as DateTimeConverter;
use Magento\Store\Model\Store;

class Pickupdate extends \Magento\Framework\Model\AbstractModel implements PickupdateInterface
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

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Ziffity\Pickupdate\Model\ResourceModel\Pickupdate');
        $this->setIdFieldName('pickupdate_id');
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
        PickupdateConfigProvider $configProvider,
        \Ziffity\Pickupdate\Model\PickupDate\Validator $dateValidator,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->tintervalResourceModel = $tintervalResourceModel;
        $this->tintervalFactory       = $tintervalFactory;
        $this->pickupHelper           = $pickupHelper;
        $this->dateFactory            = $dateFactory;
        $this->orderRepository        = $orderRepository;
        $this->configProvider         = $configProvider;
        $this->dateValidator          = $dateValidator;
        $this->messageManager         = $messageManager;
    }

    /**
     * Prepare and save data
     *
     * @param array                      $data
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    public function prepareForSave($data, $order)
    {
        $data = $this->preparePostData($data);
        if (!$data) {
            return false;
        }

        if ($order->getId()) {
            $this->_getResource()->load($this, $order->getId(), 'order_id');
        } elseif ($order->getIncrementId()) {
            $this->_getResource()->load($this, $order->getIncrementId(), 'increment_id');
        }
        if ($this->getId() && !isset($data['form_key'])) {
            return false;
        }

        if ($data['date']) {
            // convert from js date format to php date format Y-m-d
            $convertedDate = $this->covertDate($data['date'], PickupdateConfigProvider::OUTPUT_DATE_FORMAT);
            $data['date'] = $convertedDate;
        }

        if ($data['tinterval_id']) {
            /* load Time interval by ID and combine it to string */
            $tint = $this->tintervalFactory->create();
            $this->tintervalResourceModel->load($tint, (int)$data['tinterval_id']);
            $data['time'] = $tint->getTimeFrom() . " - " . $tint->getTimeTo();
        }
        $this->addData($data);
        $this->setData('order_id', $order->getId());
        $this->setData('increment_id', $order->getIncrementId());

        return true;
    }

    /**
     * @param $date
     * @param $format
     *
     * @return null|string
     */
    public function covertDate($date, $format)
    {
        $filter         = $this->dateFactory->create(['format' => $format]);
        $convertedDate  = $filter->inputFilter($date);
        $dateValidation = \DateTime::createFromFormat(DateTimeConverter::DATE_PHP_FORMAT, $convertedDate);
        if (!$dateValidation && $format == PickupdateConfigProvider::OUTPUT_DATE_FORMAT) {
            return $this->covertDate($date, $this->configProvider->getPickerDateFormat());
        }
        if (!$dateValidation) {
            // data is invalid
            return null;
        }

        return $convertedDate;
    }

    /**
     * Prepare and validate data
     *
     * @param array $data
     *
     * @return false|array    false if no need to save
     */
    public function preparePostData(array $data)
    {
        if (!isset($data['date']) || $data['date'] == '0000-00-00' || !$data['date']) {
            $data['date'] = null;
        }
        if (!isset($data['tinterval_id']) || !$data['tinterval_id']) {
            $data['tinterval_id'] = null;
        }
        if (!isset($data['comment']) || !$data['comment']) {
            $data['comment'] = null;
        }

        if (!$data['date'] && !$data['tinterval_id'] && !$data['comment']) {
            return false;
        }

        return $data;
    }

    /**
     * Validate Pickupdate Data
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function validate($order)
    {
        $shippingMethod = $order->getShippingMethod();
        $customerGroup = $order->getCustomerGroupId();

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

        if ($this->getDate() && !$this->dateValidator->validate($this->getDate(), $this->getTimeWithInterval())) {
            $this->throwValidatorException(__('Pickup Date is invalid, please choose another date'));
        }

        if ($this->getDate()
            && $this->getTintervalId()
            && $this->dateValidator->disablePastTime()
        ) {
            $this->throwValidatorException(__('Pickup Time is invalid, please choose another time'));
        }
    }

    /**
     * get time with intervals
     * needed to disable past time
     *
     * @return false|int|string
     */
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

    /**
     * @param Phrase $message
     *
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function throwValidatorException($message)
    {
        $this->messageManager->addErrorMessage($message);
        throw new \Magento\Framework\Exception\ValidatorException($message);
    }

    /**
     * Check rules is Pickup Date field can be edited by customer
     *
     * @param null|string|bool|int|Store $store
     *
     * @return bool
     */
    public function isCanEditOnFront($store = null)
    {
        if ($this->pickupHelper->getStoreScopeValue('editable/is_editable', $store)) {
            switch ($this->pickupHelper->getStoreScopeValue('editable/rule_activation', $store)) {
                case 'both':
                    return $this->checkDateRule() && $this->checkOrderStatus();
                case 'one_of':
                    return $this->checkDateRule() || $this->checkOrderStatus();
                case 'status':
                    return $this->checkOrderStatus();
                case 'date':
                    return $this->checkDateRule();
            }
        }
        return false;
    }

    /**
     * Pickup Date can be edited up tp $period days
     * If order don't have Pickup Date then allow to edit
     *
     * @param null|string|bool|int|Store $store
     *
     * @return bool
     */
    public function checkDateRule($store = null)
    {
        $pickupDate = strtotime($this->getDate());
        if ($pickupDate) {
            $period = (int)$this->pickupHelper->getStoreScopeValue('editable/period', $store);

            /**
             * DD - today                      the remaining time for pickuping in seconds;
             * ceil(seconds / 60 / 60 / 24)    convert to days with round up;
             * Days > $period                  is Pickup Date can be edited;
             */
            return (ceil(($pickupDate - time()) / 60 / 60 / 24) > $period);
        }

        return true;
    }

    /**
     * Is Order have status for reschedule Pickup Date
     *
     * @param null|string|bool|int|Store $store
     *
     * @return bool
     */
    public function checkOrderStatus($store = null)
    {
        $status = $this->getOrder()->getStatus();
        $rescheduleStatus = explode(',', $this->pickupHelper->getStoreScopeValue('editable/order_status', $store));

        return is_array($rescheduleStatus) && in_array($status, $rescheduleStatus);
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        if (!$this->_getData('order')) {
            $order = $this->orderRepository->get($this->getOrderId());
            $this->setData('order', $order);
        }

        return $this->_getData('order');
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->setData('order', $order);
        return $this;
    }

    /**
     * Get formatted date for display
     *
     * @return string
     */
    public function getFormattedDate()
    {
        if ($this->_getData('formatted_date') === null && $this->getDate() && $this->getDate() != '0000-00-00') {
            $format = $this->pickupHelper->getStoreScopeValue('date_field/format');
            $date = $this->pickupHelper->convertDateOutput($this->getDate(), $format);
            $this->setData('formatted_date', $date);
        }

        return $this->_getData('formatted_date');
    }

    /**
     * Prepare Comment for display
     *
     * @return string
     */
    public function getFormattedComment()
    {
        if ($this->_getData('formatted_comment') === null) {
             $comment = nl2br(htmlentities(
                 preg_replace('/\$/', '\\\$', $this->getComment()),
                 ENT_COMPAT,
                 "UTF-8"
             ));
             $this->setData('formatted_comment', $comment);
        }

        return $this->_getData('formatted_comment');
    }

    /**
     * Returns Pickupdate ID
     *
     * @return int
     */
    public function getPickupdateId()
    {
        return $this->_getData(self::PICKUPDATE_ID);
    }

    /**
     * @param int $pickupdateId
     *
     * @return $this
     */
    public function setPickupdateId($pickupdateId)
    {
        $this->setData(self::PICKUPDATE_ID, $pickupdateId);

        return $this;
    }

    /**
     * Returns Order ID
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::ORDER_ID, $orderId);

        return $this;
    }

    /**
     * Returns Order IncrementId
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->_getData(self::INCREMENT_ID);
    }

    /**
     * @param string $incrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        $this->setData(self::INCREMENT_ID, $incrementId);

        return $this;
    }

    /**
     * Returns date
     *
     * @return string
     */
    public function getDate()
    {
        $date = $this->_getData(self::DATE);
        if ($date == '0000-00-00') {
            return null;
        }
        return $date;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->setData(self::DATE, $date);

        return $this;
    }

    /**
     * Returns Time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->_getData(self::TIME);
    }

    /**
     * @param string $time
     *
     * @return $this
     */
    public function setTime($time)
    {
        $this->setData(self::TIME, $time);

        return $this;
    }

    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_getData(self::COMMENT);
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->setData(self::COMMENT, $comment);

        return $this;
    }

    /**
     * Returns reminder
     *
     * @return int
     */
    public function getReminder()
    {
        return $this->_getData(self::REMINDER);
    }

    /**
     * @param int $reminder
     *
     * @return $this
     */
    public function setReminder($reminder)
    {
        $this->setData(self::REMINDER, $reminder);

        return $this;
    }

    /**
     * Returns time interval ID
     *
     * @return int
     */
    public function getTintervalId()
    {
        return $this->_getData(self::TINTERVAL_ID);
    }

    /**
     * @param int $tintervalId
     *
     * @return $this
     */
    public function setTintervalId($tintervalId)
    {
        $this->setData(self::TINTERVAL_ID, $tintervalId);

        return $this;
    }

    /**
     * Returns is active
     *
     * @return int
     */
    public function getActive()
    {
        return $this->_getData(self::ACTIVE);
    }

    /**
     * @param int $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->setData(self::ACTIVE, $active);

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentStoreDate()
    {
        $currencyDateTime = $this->pickupHelper->getPickupDataWithOffsets();
        return $currencyDateTime->getDate()->toString('MM/dd/yyyy');
    }
}
