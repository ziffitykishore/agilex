<?php

/**
 * Product:       Xtento_OrderExport (2.6.6)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-09-18T14:52:22+00:00
 * Last Modified: 2015-08-09T14:36:11+00:00
 * File:          app/code/Xtento/OrderExport/Controller/Adminhtml/Destination/NewAction.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Controller\Adminhtml\Destination;

class NewAction extends \Xtento\OrderExport\Controller\Adminhtml\Destination
{
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}