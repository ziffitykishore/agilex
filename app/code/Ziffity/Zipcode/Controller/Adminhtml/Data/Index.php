<?php

namespace Ziffity\Zipcode\Controller\Adminhtml\Data;

use Ziffity\Zipcode\Controller\Adminhtml\Data;

class Index extends Data
{

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
