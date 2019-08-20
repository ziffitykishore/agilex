<?php

namespace Ziffity\Deliverydate\Model;

use Amasty\Deliverydate\Model\DeliverydateConfigProvider as DeliveryConfigProvider;
use Amasty\Deliverydate\Helper\Data as DeliverydateHelper;
use Amasty\Deliverydate\Model\ResourceModel\Deliverydate\CollectionFactory as DeliverydateCollectionFactory;
use Amasty\Deliverydate\Model\ResourceModel\Dinterval\CollectionFactory as DintervalCollectionFactory;
use Amasty\Deliverydate\Model\ResourceModel\Holidays\CollectionFactory as HolidaysCollectionFactory;
use Amasty\Deliverydate\Model\ResourceModel\Tinterval\CollectionFactory as TintervalCollectionFactory;
use Magento\Checkout\Model\ConfigProviderInterface;


class DeliverydateConfigProvider extends DeliveryConfigProvider
{
    
    const OUTPUT_DATE_FORMAT = 'MM/dd/yyyy';

    const ORDER_STATUS_CANCELLED = 'canceled';

    static private $quotaTime = [];

    private $dayExceptions;

    /**
     * @var DeliverydateHelper
     */
    private $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var TintervalCollectionFactory
     */
    private $tintervalFactory;

    /**
     * @var \Magento\Framework\Data\Form\Filter\DateFactory
     */
    private $dateFactory;

    /**
     * @var DeliverydateCollectionFactory
     */
    private $deliverydateCollectionFactory;

    /**
     * @var DintervalCollectionFactory
     */
    private $dintervalCollectionFactory;

    /**
     * @var HolidaysCollectionFactory
     */
    private $holidaysCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    protected $orderRepository;

    /**
     * DeliverydateConfigProvider constructor.
     *
     * @param DeliverydateHelper $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TintervalCollectionFactory $tintervalFactory
     * @param \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory
     * @param DeliverydateCollectionFactory $deliverydateCollectionFactory
     * @param DintervalCollectionFactory $dintervalCollectionFactory
     * @param HolidaysCollectionFactory $holidaysCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        DeliverydateHelper $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TintervalCollectionFactory $tintervalFactory,
        \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory,
        DeliverydateCollectionFactory $deliverydateCollectionFactory,
        DintervalCollectionFactory $dintervalCollectionFactory,
        HolidaysCollectionFactory $holidaysCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($helper, $date, $tintervalFactory, $dateFactory, $deliverydateCollectionFactory, $dintervalCollectionFactory, $holidaysCollectionFactory, $checkoutSession, $storeManager, $productCollectionFactory);
        $this->helper = $helper;
        $this->date = $date;
        $this->tintervalFactory = $tintervalFactory;
        $this->dateFactory = $dateFactory;
        $this->deliverydateCollectionFactory = $deliverydateCollectionFactory;
        $this->dintervalCollectionFactory = $dintervalCollectionFactory;
        $this->holidaysCollectionFactory = $holidaysCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
    }
    
    
    public function getConfigQuotaTime($minDays)
    {
                
        if (array_key_exists('d' . $minDays, self::$quotaTime)) {
            return self::$quotaTime['d' . $minDays];
        }
        $restrictedDates = [];

        if ($this->helper->getDefaultScopeValue('quota/quota_type')) {

            // 24 h. * 60 min. * 60 sec. = 86400 sec.
            $min = $this->date->timestamp() + 86400 * ((int)$minDays - 1);
            $collection = $this->deliverydateCollectionFactory->create();
            $collection->getOlderThan($this->date->date('Y-m-d', $min));

            if ($collection->getSize() > 0) {
                $dates = [];
                foreach ($collection as $delivery) {
                    if ($this->getOrderStatus($delivery->getOrderId()) != self::ORDER_STATUS_CANCELLED) {
                        $dates[] = $delivery->getDate().'-'.$delivery->getTime().'_'.$delivery->getTintervalId();
                    }
                }

                $deliveries = array_count_values($dates);

                foreach ($deliveries as $date => $count) {
                    $quota = $this->getTimeQuota(substr($date, 0,10));
                    if ($quota && $count >= $quota) {
                        $restrictedDates
                        [$this->date->date('Y', substr($date, 0,10))]
                        [$this->date->date('n', substr($date, 0,10))]
                        [$this->date->date('d', substr($date, 0,10))]
                        [substr($date, 11)]
                            = true;
                    }
                }
            }
        }

        return self::$quotaTime['d' . $minDays] = $restrictedDates;
    }


    public function getTimeQuota($date)
    {
        $quota = 0;
        switch ($this->helper->getWebsiteScopeValue('quota/quota_type')) {
            case 'day':
                $quota = $this->helper->getWebsiteScopeValue('quota/per_day');
                break;
            case 'week_day':
                $weekDay = $this->date->date('N', $date);
                if ($weekDay >= 1 && $weekDay <= 7) {
                    $quota = $this->helper->getWebsiteScopeValue('quota/per' . $weekDay);
                }
                break;
            case 'time_slot':
                $quota = $this->helper->getStoreScopeValue('quota/per_time_slot');
                break;
        }
        return (int)$quota;
    }


    public function getTimeOffset()
    {
        $timeOffsetInMin = $this->helper->getWebsiteScopeValue('time_field/offset_disabled');
        $timeOffsetInHour = $timeOffsetInMin / 60;

        return (int) $timeOffsetInHour;
    }

    public function getOrderStatus($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $state = $order->getState();
        return $state;
    }
}
