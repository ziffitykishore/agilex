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

class ModelSaveBefore extends ModelSaveBase implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var AbstractModel $object */
        $object = $observer->getEvent()->getData('object');
        $origUrfId = $object->getOrigData(Data::URF_ID_FIELD);
        $urfId = $object->getData(Data::URF_ID_FIELD);
        //if ($object->isObjectNew()) { // if object is new urf_id will be set after save
        if ($object->isObjectNew()) $object->unsetData(Data::URF_ID_FIELD);
        if ($origUrfId && $origUrfId === $urfId) {
            // if there was no original urf_id that has been changed, then no need to set it here
            return;
        }

        if (!$object->isObjectNew()) $this->_execute($object, false);
    }
}
