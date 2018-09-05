<?php
namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Ziffity\Webforms\Controller\Adminhtml\Coin;

class Index extends Coin
{
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
