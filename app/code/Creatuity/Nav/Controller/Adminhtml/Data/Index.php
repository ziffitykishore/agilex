<?php

namespace Creatuity\Nav\Controller\Adminhtml\Data;

use Creatuity\Nav\Controller\Adminhtml\Data;

class Index extends Data
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $resultPage->getConfig()->getTitle()->prepend(__('Navision Log'));
        return $resultPage;
    }
}
