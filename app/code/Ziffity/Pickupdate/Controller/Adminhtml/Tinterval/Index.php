<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Tinterval;


class Index extends \Ziffity\Pickupdate\Controller\Adminhtml\Tinterval
{

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        return $resultPage;
    }
}
