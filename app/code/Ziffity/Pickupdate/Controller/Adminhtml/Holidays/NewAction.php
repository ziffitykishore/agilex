<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml\Holidays;

class NewAction extends \Ziffity\Pickupdate\Controller\Adminhtml\Holidays
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
