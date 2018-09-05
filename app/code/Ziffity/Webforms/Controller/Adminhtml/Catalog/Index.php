<?php
namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Controller\Adminhtml\Catalog;

class Index extends Catalog
{
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
