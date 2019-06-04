<?php

namespace Ziffity\PickupdateOR\Model\PickupDate;

use Ziffity\Pickupdate\Model\PickupDate\Validator as PickupValidator;
use Ziffity\Pickupdate\Helper\Data as PickupdateHelper;
use Ziffity\Pickupdate\Model\PickupdateConfigProvider;
use Magento\Framework\Stdlib\DateTime as DateTimeConverter;
use Magento\Framework\Stdlib\DateTime\DateTime;


class Validator extends PickupValidator
{
    /**
     * @var null|int
     */
    private $minDayConfig = null;

    /**
     * @var DateDataObject
     */
    private $currentPickupDate;

    /**
     * @var PickupdateHelper
     */
    private $helper;

    /**
     * @var DateTime
     */
    private $dateLib;

    /**
     * @var \Magento\Framework\Data\Form\Filter\DateFactory
     */
    private $dateFactory;

    /**
     * @var PickupdateConfigProvider
     */
    private $configProvider;
    /**
     * @var PickupdateHelper
     */
    private $pickupHelper;

    public function __construct(
        PickupdateHelper $helper,
        DateTime $dateLib,
        \Magento\Framework\Data\Form\Filter\DateFactory $dateFactory,
        PickupdateConfigProvider $configProvider,
        \Ziffity\Pickupdate\Model\PickupDate\DateDataObjectFactory $dataObjectFactory,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper
    ) {
        parent::__construct($helper, $dateLib, $dateFactory, $configProvider, $dataObjectFactory, $pickupHelper);
        $this->helper = $helper;
        $this->dateLib = $dateLib;
        $this->dateFactory = $dateFactory;
        $this->configProvider = $configProvider;
        $this->currentPickupDate = $dataObjectFactory->create();
        $this->pickupHelper = $pickupHelper;

        $this->todayTimestamp = $this->dateLib->timestamp(date('j F Y'));
        $this->currentTimestamp = $this->dateLib->timestamp()
            + 3600 * $this->pickupHelper->getDefaultScopeValue('general/offset');
    }

    /**
     * Validate Pickup Date
     *
     * @param string $pickupDate date in mysql format YYYY-mm-dd
     * @param $timeWithInterval
     * @return bool
     */
    public function validateTimeSlot($timeSlot, $pickupDate, $timeWithInterval)
    {
        
        if (!$pickupDate) {
            return false;
        }
        $this->setCurrentPickupDate($pickupDate, $timeWithInterval);
        switch (true) {
            case $this->disableSameDay():
            case $this->disableNextDay():
            case $this->restrictByTimeSlotQuota($timeSlot):
            case $this->restrictDateLessToday():
                return false;
            case $this->notRestrictWorkingDays():
                return true;
            case $this->minDays():
            case $this->maxDays():
            case $this->restrictDateInterval():
            case $this->restrictHolidays():
            case $this->daysOfWeek():
                return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function disablePastTime()
    {
        if ($this->helper->getStoreScopeValue('time_field/enabled_time')) {
            $selectedTime = $this->currentPickupDate->getTimeWithInterval();
            $isValid = $selectedTime <= $this->currentTimestamp;
        } else {
            $isValid = $this->currentTimestamp <= $this->todayTimestamp;
        }

        return $isValid;
    }

    /**
     * @param string $pickupDate
     * @param $timeWithInterval
     */
    private function setCurrentPickupDate($pickupDate, $timeWithInterval)
    {
        $this->currentPickupDate->setDate($pickupDate);
        $timestamp = $this->dateLib->timestamp($pickupDate);
        $this->currentPickupDate->setObject(new \Zend_Date($timestamp, \Zend_Date::TIMESTAMP));
        $this->currentPickupDate->setTimestamp($timestamp);
        $this->currentPickupDate->setYear($this->dateLib->date('Y', $timestamp));
        $this->currentPickupDate->setMonth($this->dateLib->date('n', $timestamp));
        $this->currentPickupDate->setDay($this->dateLib->date('d', $timestamp));
        $this->currentPickupDate->setTimeWithInterval($timeWithInterval);
    }

    /**
     * @return bool
     */
    private function disableSameDay()
    {
        if ($this->helper->getStoreScopeValue('general/enabled_same_day')
            && $this->currentPickupDate->getObject()->isToday()
        ) {
            $disableAfter = $this->setDateTime($this->helper->getStoreScopeValue('general/same_day'));
            $now = $this->dateLib->timestamp();
            $offset = $this->helper->getTimeOffset();
            if ($offset > 0) {
                $now = $this->dateLib->timestamp('+' . $offset . 'hour');
            } elseif ($offset < 0 || strpos($offset, '-') !== false) {
                $now = $this->dateLib->timestamp('-' . $offset . 'hour');
            }

            if ($now > $disableAfter) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function disableNextDay()
    {
        if ($this->helper->getStoreScopeValue('general/enabled_next_day')
            && $this->currentPickupDate->getObject()->isTomorrow()
        ) {
            $disableAfter = $this->setDateTime($this->helper->getStoreScopeValue('general/next_day'));
            $now = $this->dateLib->timestamp();
            $offset = $this->helper->getTimeOffset();
            if ($offset > 0) {
                $now = $this->dateLib->timestamp('+' . $offset . 'hour');
            } elseif ($offset < 0 || strpos($offset, '-') !== false) {
                $now = $this->dateLib->timestamp('-' . $offset . 'hour');
            }

            if ($now > $disableAfter) {
                return true;
            }
        }
        return false;
    }

    /**
     * make timestamp for compare time
     *
     * @param string $timeString
     *
     * @return false|int
     */
    private function setDateTime($timeString)
    {
        list($hour, $minute, $second) = explode(',', $timeString);

        return mktime($hour, $minute, $second);
    }

    /**
     * Is need to restrict day by Quota
     * Is limit for shipping quota of day is not exceeded
     *
     * @returns boolean
     */
    private function restrictByTimeSlotQuota($timeSlot)
    {
        $quota = $this->configProvider->getConfigQuotaTime($this->getMinDayConfig());
        $date = $this->currentPickupDate;

        return $this->validateQuoteByDateTime($timeSlot, $quota, $date);
    }

    /**
     * @param array $quota
     * @param \Ziffity\Pickupdate\Model\PickupDate\DateDataObject $date
     *
     * @return bool
     */
    private function validateQuoteByDateTime($timeSlot, $quota, $date)
    {
        $year = $date->getYear();
        $month = $date->getMonth();
        $day = $date->getDay();

        return isset($quota[$year][$month][$day][$timeSlot]) && $quota[$year][$month][$day][$timeSlot];
    }

    /**
     * @return bool
     */
    private function restrictDateLessToday()
    {
        return $this->dateLib->date('Ymd', $this->currentPickupDate->getTimestamp()) < $this->dateLib->date('Ymd');
    }

    /**
     * @return bool
     */
    private function notRestrictWorkingDays()
    {
        $days = $this->configProvider->getDayException();

        return $this->restrictDate($days['workingdays']);
    }

    /**
     * Is need to restrict day by Date Interval
     *
     * @returns boolean
     */
    private function restrictDateInterval()
    {
        $isNeedRestrict = false;
        foreach ($this->configProvider->getDateIntervals() as $interval) {
            $from = $interval['from'];
            $to = $interval['to'];

            if ($from['year'] == 0 || $to['year'] == 0) {
                // is interval for each year
                $from['year'] = $to['year'] = $this->currentPickupDate->getYear();
            }
            if ($from['month'] == 0 || $to['month'] == 0) {
                // is interval for each month
                $from['month'] = $to['month'] = $this->currentPickupDate->getMonth();
            }
            $inputDate = $this->currentPickupDate->getTimestamp();
            $fromDate  = $this->dateLib->timestamp($from['year'] . '-' . $from['month'] . '-' . $from['day']);
            $toDate  = $this->dateLib->timestamp($to['year'] . '-' . $to['month'] . '-' . $to['day']);
            if ($fromDate > $toDate) {
                // revert interval
                // restrict all days in same year from fromDate and to toDate
                if (($from['year'] <= $this->currentPickupDate->getYear()
                        && $to['year'] >= $this->currentPickupDate->getYear())
                    && ($inputDate >= $fromDate || $inputDate <= $toDate)
                ) {
                    $isNeedRestrict = true;
                    break;
                }
            } else {
                if ($inputDate >= $fromDate && $inputDate <= $toDate) {
                    $isNeedRestrict = true;
                    break;
                }
            }
        }

        return $isNeedRestrict;
    }

    /**
     * Is need to restrict day as Holidays
     *
     * @returns boolean
     */
    private function restrictHolidays()
    {
        $days = $this->configProvider->getDayException();

        return $this->restrictDate($days['holidays']);
    }

    /**
     * Is need to restrict day
     *
     * @param array $restrict
     * @returns boolean
     */
    private function restrictDate($restrict)
    {
        $date = $this->currentPickupDate;
        $year = $date->getYear();
        $month = $date->getMonth();
        $day = $date->getDay();
        if (isset($restrict[$year][$month][$day])) {
            return $restrict[$year][$month][$day];
        }
        // 0 - for all month
        if (isset($restrict[$year][0][$day])) {
            return $restrict[$year][0][$day];
        }
        // 0 - for all year
        if (isset($restrict[0][$month][$day])) {
            return $restrict[0][$month][$day];
        }
        if (isset($restrict[0][0][$day])) {
            return $restrict[0][0][$day];
        }

        return false;
    }

    /**
     * Is current day of the week restricted
     *
     * @return bool
     */
    private function daysOfWeek()
    {
        $days = $this->configProvider->getDisabledDays();

        return $days && in_array($this->dateLib->date('N', $this->currentPickupDate->getTimestamp()), $days);
    }

    /**
     * Validate Minimal Pickup Interval
     *
     * @return bool
     */
    private function minDays()
    {
        // 24 h. * 60 min. * 60 sec. = 86400 sec
        $minDay = $this->todayTimestamp + $this->helper->getMinDays() * 86400;

        return $this->currentPickupDate->getTimestamp() < $minDay;
    }

    /**
     * @return int|null
     */
    private function getMinDayConfig()
    {
        if ($this->minDayConfig === null) {
            $this->minDayConfig = $this->helper->getMinDays();
        }

        return $this->minDayConfig;
    }

    /**
     * Validate Maximal Pickup Interval
     *
     * @return bool
     */
    private function maxDays()
    {
        $config = (int)$this->helper->getStoreScopeValue('general/max_days');
        if ($config <= 0) {
            return false;
        }
        // 24 h. * 60 min. * 60 sec. = 86400 sec
        $maxDay = $this->todayTimestamp + $config * 86400;

        return $this->currentPickupDate->getTimestamp() > $maxDay;
    }    
}


