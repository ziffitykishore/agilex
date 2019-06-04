<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml\Dinterval;

class NewAction extends \Ziffity\Pickupdate\Controller\Adminhtml\Dinterval
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
