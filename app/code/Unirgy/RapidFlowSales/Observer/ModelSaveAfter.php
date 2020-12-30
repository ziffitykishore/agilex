<?php

/**
 * Created by pp
 *
 * @project pp-dev-2-unirgy-ext
 */
namespace Unirgy\RapidFlowSales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Unirgy\RapidFlowSales\Helper\Data;

class ModelSaveAfter extends ModelSaveBase implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var AbstractModel $object */
        $object = $observer->getEvent()->getData('object');
        if($object->hasData(Data::URF_ID_FIELD)){
            return;
        }

        $this->_execute($object);
    }
}
