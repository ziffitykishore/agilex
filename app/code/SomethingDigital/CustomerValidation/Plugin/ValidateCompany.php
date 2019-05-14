<?php

namespace SomethingDigital\CustomerValidation\Plugin;

use Magento\Company\Controller\Account\CreatePost;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ValidateCompany
{
    private $customerFactory;
    private $messageManager;
    private $result;
    private $redirect;
    private $customerSession;
    private $scopeConfig;

    public function __construct(
        CustomerFactory $customerFactory,
        UrlFactory $urlFactory,
        ManagerInterface $messageManager,
        ResultFactory $result,
        RedirectInterface $redirect,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerFactory = $customerFactory;
        $this->urlModel = $urlFactory->create();
        $this->messageManager = $messageManager;
        $this->resultRedirect = $result;
        $this->redirect = $redirect;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundExecute(CreatePost $subject, callable $proceed)
    {
        if ($this->scopeConfig->getValue('customer/create_account/travers_account_id_validation', ScopeInterface::SCOPE_STORE)) {
            return $proceed();
        }
        $resultRedirect = $this->resultRedirect->create(
            ResultFactory::TYPE_REDIRECT
        );
        $traversAccountId = $subject->getRequest()->getParam('travers_account_id');

        if (!empty($traversAccountId)) {
            $customerCollection = $this->customerFactory->create()->getCollection()
                ->addAttributeToFilter('travers_account_id', $traversAccountId)
                ->load();
            foreach ($customerCollection as $customer) {
                $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                $message = __(
                    'There is already an account with this account number.'
                );
                $this->customerSession->setCustomerFormData($subject->getRequest()->getParams());
                $this->messageManager->addError($message);
                $resultRedirect->setUrl($this->redirect->error($url));
                return $resultRedirect;
            }
        }
        return $proceed();
    }
}