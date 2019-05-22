<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */


namespace Amasty\Deliverydate\Controller\Adminhtml\Tinterval;

class NewAction extends \Amasty\Deliverydate\Controller\Adminhtml\Tinterval
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
