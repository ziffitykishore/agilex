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
    protected $resultRedirectFactory;

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
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    public function aroundExecute(\Magento\Customer\Controller\Account\ForgotPasswordPost $subject, \Closure $proceed)
    {
        $messageManager = $this->context->getMessageManager();
        try {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $email = (string)$this->context->getRequest()->getPost('email');

            $websiteId = $this->storeManager->getStore()->getWebsiteId();

            // load customer by email
            $customer = $this->customerRepository->get($email, $websiteId);

            if ($customer->getCustomAttribute('account_type')->getValue() == "company") {
                $messageManager->addErrorMessage("Cannot reset password for a company account type");
                return $resultRedirect->setPath('*/*/forgotpassword');
            } else {
                return $proceed();
            }
        } catch (NoSuchEntityException $exception) {
            // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
        }
        $messageManager->addSuccessMessage($this->getSuccessMessage($email));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Retrieve success message
     *
     * @param string $email
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($email)
    {
        // Redefined the menthod, As unable to use the protected method
        return __(
            'If there is an account associated with %1 you will receive an email with a link to reset your password.',
            $this->escaper->escapeHtml($email)
        );
    }
}
