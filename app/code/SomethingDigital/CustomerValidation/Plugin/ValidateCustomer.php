<?php

namespace SomethingDigital\CustomerValidation\Plugin;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\CustomerValidation\Model\CustomerApi;
use Psr\Log\LoggerInterface;
use SomethingDigital\CustomerValidation\Helper\Validation;

class ValidateCustomer
{
    private $customerFactory;
    private $messageManager;
    private $result;
    private $redirect;
    private $customerSession;
    private $scopeConfig;
    private $customerApi;
    private $customerValidationHelper;
    protected $logger;

    public function __construct(
        CustomerFactory $customerFactory,
        UrlFactory $urlFactory,
        ManagerInterface $messageManager,
        ResultFactory $result,
        RedirectInterface $redirect,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        CustomerApi $customerApi,
        LoggerInterface $logger,
        Validation $customerValidationHelper
    ) {
        $this->customerFactory = $customerFactory;
        $this->urlModel = $urlFactory->create();
        $this->messageManager = $messageManager;
        $this->resultRedirect = $result;
        $this->redirect = $redirect;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->customerApi = $customerApi;
        $this->logger = $logger;
        $this->customerValidationHelper = $customerValidationHelper;
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
        $accountZipCode = $subject->getRequest()->getParam('account_zip_code');
        
        $message = $this->customerValidationHelper->validate($traversAccountId, $accountZipCode);

        if ($message != '') {
            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            $this->customerSession->setCustomerFormData($subject->getRequest()->getParams());
            $this->messageManager->addError($message);
            $resultRedirect->setUrl($this->redirect->error($url));
            return $resultRedirect;
        }
        
        return $proceed();
    }
}