<?php

namespace Ziffity\PickupdateOR\Model;

use Ziffity\Pickupdate\Model\PickupdateConfigProvider as PickupConfigProvider;
use Ziffity\Pickupdate\Helper\Data as PickupdateHelper;
use Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\CollectionFactory as PickupdateCollectionFactory;
use Ziffity\Pickupdate\Model\ResourceModel\Dinterval\CollectionFactory as DintervalCollectionFactory;
use Ziffity\Pickupdate\Model\ResourceModel\Holidays\CollectionFactory as HolidaysCollectionFactory;
use Ziffity\Pickupdate\Model\ResourceModel\Tinterval\CollectionFactory as TintervalCollectionFactory;
use Magento\Checkout\Model\ConfigProviderInterface;


class PickupdateConfigProvider extends PickupConfigProvider
{
    
    const OUTPUT_DATE_FORMAT = 'MM/dd/yyyy';

    static private $quotaTime = [];

    private $dayExceptions;

    /**
     * @var PickupdateHelper
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
     * @var PickupdateCollectionFactory
     */
    private $pickupdateCollectionFactory;

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

    /**
     * PickupdateConfigProvider constructor.
     *
     * @param PickupdateHelper $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TintervalCollectionFactory $tintervalFactory
     * @param \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory
     * @param PickupdateCollectionFactory $pickupdateCollectionFactory
     * @param DintervalCollectionFactory $dintervalCollectionFactory
     * @param HolidaysCollectionFactory $holidaysCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        PickupdateHelper $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TintervalCollectionFactory $tintervalFactory,
        \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory,
        PickupdateCollectionFactory $pickupdateCollectionFactory,
        DintervalCollectionFactory $dintervalCollectionFactory,
        HolidaysCollectionFactory $holidaysCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        parent::__construct($helper, $date, $tintervalFactory, $dateFactory, $pickupdateCollectionFactory, $dintervalCollectionFactory, $holidaysCollectionFactory, $checkoutSession, $storeManager, $productCollectionFactory);
        $this->helper = $helper;
        $this->date = $date;
        $this->tintervalFactory = $tintervalFactory;
        $this->dateFactory = $dateFactory;
        $this->pickupdateCollectionFactory = $pickupdateCollectionFactory;
        $this->dintervalCollectionFactory = $dintervalCollectionFactory;
        $this->holidaysCollectionFactory = $holidaysCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        
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
            $collection = $this->pickupdateCollectionFactory->create();
            $collection->getOlderThan($this->date->date('Y-m-d', $min));

            if ($collection->getSize() > 0) {
                $dates = [];
                foreach ($collection as $pickup) {
                    $dates[] = $pickup->getDate().'-'.$pickup->getTime();
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
                $quota = $this->helper->getWebsiteScopeValue('quota/per_time_slot');
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
    
}
