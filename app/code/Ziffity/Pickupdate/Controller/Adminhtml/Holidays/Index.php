<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml\Holidays;


class Index extends \Ziffity\Pickupdate\Controller\Adminhtml\Holidays
{

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        return $resultPage;
    }
}
