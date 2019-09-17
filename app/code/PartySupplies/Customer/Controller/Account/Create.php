<?php

namespace PartySupplies\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Company Registration Page
 */
class Create extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('createNewCompanyAccount'));
        return $resultPage;
    }
}
