<?php
namespace Ziffity\Blockcustomers\Controller\Adminhtml\Data;

use Ziffity\Blockcustomers\Controller\Adminhtml\Data;

class Index extends Data
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Blockcustomers::data');
        $resultPage->getConfig()->getTitle()->prepend(__('Blocked Customers'));
        return $resultPage;        
    }
}
