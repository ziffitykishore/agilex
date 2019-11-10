<?php

namespace PartySupplies\QuickOrder\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\QuickOrder\Helper\Data;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $storeId = $this->_helperData->getStore()->getStoreId();
        $identifier = trim($this->_request->getPathInfo(), '/');
        if ($this->_helperData->checkPermissionAccess() === false
            || in_array($identifier, ['quickorder', 'quickorder/index/index'])) {
            $this->_redirect('customer/account/login');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('quickorder_index_index');
        $resultPage->getConfig()->getTitle()->set($this->_helperData->getPageTitle($storeId));

        return $resultPage;
    }
}
