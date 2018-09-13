<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Controller\Adminhtml\Data;

class Add extends Data
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
