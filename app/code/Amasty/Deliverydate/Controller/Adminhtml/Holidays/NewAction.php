<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */


namespace Amasty\Deliverydate\Controller\Adminhtml\Holidays;

class NewAction extends \Amasty\Deliverydate\Controller\Adminhtml\Holidays
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
