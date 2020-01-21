<?php

namespace SomethingDigital\CustomerStoreRedirection\Controller\Redirect;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    public function execute()
    {
        $this->messageManager->addNotice( __('You have been redirected to the store associated with your account. Please log in again.') );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/customer/account/login/');

        return $resultRedirect;
    }
}
