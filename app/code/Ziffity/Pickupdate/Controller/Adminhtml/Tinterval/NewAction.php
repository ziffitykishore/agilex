<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Tinterval;

class NewAction extends \Ziffity\Pickupdate\Controller\Adminhtml\Tinterval
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
