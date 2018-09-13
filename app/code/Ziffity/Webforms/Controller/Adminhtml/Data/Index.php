<?php
namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Controller\Adminhtml\Data;

class Index extends Data
{
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
