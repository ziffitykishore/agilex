<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Dinterval;

class Index extends \Ziffity\Pickupdate\Controller\Adminhtml\Dinterval
{

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page */
        return $this->_initAction();
    }
}
