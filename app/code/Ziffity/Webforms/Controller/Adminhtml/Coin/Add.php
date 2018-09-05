<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Ziffity\Webforms\Controller\Adminhtml\Coin;

class Add extends Coin
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
