<?php

namespace SomethingDigital\CustomerStoreRedirection\Plugin;

use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Controller\Account\LoginPost;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator;

class LoginRedirect
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResultFactory
     */
    protected $result;

    /**
    * @param Session $customerSession
    * @param Validator $formKeyValidator
    * @param CustomerRepositoryInterface $customerRepository
    * @param StoreManagerInterface $storeManager
    * @param ResultFactory $result
    **/
    public function __construct(
        Session $customerSession,
        Validator $formKeyValidator,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        ResultFactory $result
    ) {
        $this->session = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->result = $result;
    }

    public function aroundExecute(LoginPost $subject, callable $proceed)
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($subject->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->result->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($subject->getRequest()->isPost()) {
            $login = $subject->getRequest()->getPost('login');
            if (!empty($login['username'])) {
                try {
                    $customer = $this->customerRepository->get($login['username']);
                } catch (NoSuchEntityException $e) {
                    return $proceed();
                }

                $customerStoreId = $customer->getStoreId();
                $customerStore = $this->storeManager->getStore($customerStoreId);
                $currectStore = $this->storeManager->getStore();

                if ($customerStore->getId() != $currectStore->getId()) {
                    $resultRedirect = $this->result->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath($customerStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'customer-store-redirection/redirect/');
                    return $resultRedirect;
                }
            }
        }
        return $proceed();
    }
}
