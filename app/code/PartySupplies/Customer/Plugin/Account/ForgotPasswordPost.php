<?php

namespace PartySupplies\Customer\Plugin\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ForgotPasswordPost
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resRedirectFactory;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     * @param Context $context
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        Context $context
    ) {
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->context = $context;
        $this->escaper = $escaper;
        $this->resRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(\Magento\Customer\Controller\Account\ForgotPasswordPost $subject, \Closure $proceed)
    {
        $messageManager = $this->context->getMessageManager();
        try {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resRedirectFactory->create();
            $email = (string)$this->context->getRequest()->getPost('email');

            $websiteId = $this->storeManager->getStore()->getWebsiteId();

            // load customer by email
            $customer = $this->customerRepository->get($email, $websiteId);

            if ($customer->getCustomAttribute('account_type')->getValue() == "company") {
                $messageManager->addErrorMessage(__("resetCompanyPasswordNotAllowed"));
                return $resultRedirect->setPath('*/*/forgotpassword');
            }
            
            $proceed();
        } catch (NoSuchEntityException $exception) {
            $messageManager->addErrorMessage($this->getErrorMessage($email));
            return $resultRedirect->setPath('*/*/forgotpassword');
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * Retrieve error message
     *
     * @param string $email
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage($email)
    {
        // Redefined the menthod, As unable to use the protected method
        return __(
            'noAccountFound',
            $this->escaper->escapeHtml($email)
        );
    }
}
