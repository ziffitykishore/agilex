<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Controller\Adminhtml\Catalog;

class Add extends Catalog
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
