<?php

namespace SomethingDigital\CompanyCredit\Plugin;

use Magento\Framework\Controller\Result\RedirectFactory;

class DisableCreditHistory
{
    protected $resultRedirectFactory;

    public function __construct(
        RedirectFactory $resultRedirectFactory
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Disable Company Credit History Page
     */
    public function aroundExecute(\Magento\CompanyCredit\Controller\History\Index $subject, callable $proceed)
    {
        return $this->resultRedirectFactory->create()->setPath('customer/account');
    }
}
