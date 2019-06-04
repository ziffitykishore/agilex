<?php

namespace Ziffity\Pickupdate\Model;

use Ziffity\Pickupdate\Helper\Data as PickupdateHelper;

class Tinterval extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var PickupdateHelper
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PickupdateHelper $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Ziffity\Pickupdate\Model\ResourceModel\Tinterval');
        $this->setIdFieldName('tinterval_id');
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        $pickupDateTime = $this->helper->getPickupDataWithOffsets();
        return $pickupDateTime->getTime()->toString('HH:mm');
    }

    /**
     * @param $optionsForCurrentDay
     * @return mixed
     */
    public function restrictCurrentTinterval($optionsForCurrentDay)
    {
        if ($this->helper->getDisabledCurrentTimeInteval() && $optionsForCurrentDay) {
            unset($optionsForCurrentDay[0]);
        }

        return $optionsForCurrentDay;
    }
}
