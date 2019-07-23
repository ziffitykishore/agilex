<?php

namespace Ziffity\Zipcode\Controller\Adminhtml\Data;

use Ziffity\Zipcode\Controller\Adminhtml\Data;

class Add extends Data
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
