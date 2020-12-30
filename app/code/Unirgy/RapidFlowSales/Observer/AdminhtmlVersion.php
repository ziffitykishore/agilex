<?php

/**
 * Created by pp
 *
 * @project pp-dev-2-unirgy-ext
 */

namespace Unirgy\RapidFlowSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Unirgy\RapidFlow\Helper\Data as HelperData;

class AdminhtmlVersion implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_helperData->addAdminhtmlVersion('Unirgy_RapidFlowSales');
    }
}
