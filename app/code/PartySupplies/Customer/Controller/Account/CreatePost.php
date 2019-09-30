<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PartySupplies\Customer\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Math\Random;
use Magento\Newsletter\Model\SubscriberFactory;
use PartySupplies\Customer\ViewModel\Register;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Phrase;
use Magento\Customer\Model\CustomerFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\Account\CreatePost
{
    /**
     * @var Random
     */
    protected $mathRandom;
    
    /**
     * @var AccountRedirect
     */
    private $accountRedirect;
    
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @var Validator
     */
    private $formKeyValidator;
    
    /**
     * @var boolean
     */
    private $isCustomerAccount;
        
    /**
     * @var boolean
     */
    private $isCompanyAccount;
    
    /**
     * @var Register
     */
    private $registerViewModel;
    
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;


    /**
     *
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerUrl $customerUrl
     * @param Registration $registration
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountRedirect $accountRedirect
     * @param Random $mathRandom
     * @param Register $registerViewModel
     * @param UploaderFactory $uploader
     * @param Validator $formKeyValidator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerUrl $customerUrl,
        Registration $registration,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        AccountRedirect $accountRedirect,
        Random $mathRandom,
        Register $registerViewModel,
        UploaderFactory $uploaderFactory,
        Validator $formKeyValidator = null,
        CustomerFactory $customerFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $scopeConfig,
            $storeManager,
            $accountManagement,
            $addressHelper,
            $urlFactory,
            $formFactory,
            $subscriberFactory,
            $regionDataFactory,
            $addressDataFactory,
            $customerDataFactory,
            $customerUrl,
            $registration,
            $escaper,
            $customerExtractor,
            $dataObjectHelper,
            $accountRedirect,
            $formKeyValidator
        );
        $this->mathRandom = $mathRandom;
        $this->registerViewModel = $registerViewModel;
        $this->uploaderFactory = $uploaderFactory;
        $this->accountRedirect = $accountRedirect;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get(Validator::class);
        $this->customerFactory = $customerFactory;
    }
    
    /**
     * Retrieve cookie manager
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
    
    /**
     * @return Redirect
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $company = $this->getRequest()->getParam('company');
        $navCustomerId = $this->getRequest()->getParam('nav_customer_id');
        
        $this->isCompanyAccount = isset($company) && !empty($company);
        $this->isCustomerAccount = isset($navCustomerId) && !empty($navCustomerId);
        
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if (!$this->isCompanyAccount && !$this->isCustomerAccount) {
            $this->messageManager->addError(__('storeRegisterationFailed_KindlyTryAgain'));
        } elseif ($this->isCustomerAccount || ($this->isCompanyAccount &&
            $this->validateResellerCertificate('reseller_certificate'))) {
            
            /* account creation code */
            
            if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }

            if (!$this->getRequest()->isPost()
                || !$this->formKeyValidator->validate($this->getRequest())
            ) {
                $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                return $this->resultRedirectFactory->create()
                    ->setUrl($this->_redirect->error($url));
            }

            $this->session->regenerateId();

            if ($this->isCustomerAccount) {
                $this->getRequest()->setParam('account_type', 'customer');
            } elseif ($this->isCompanyAccount) {
                $this->getRequest()->setParam('account_type', 'company');
            }

            try {
                $address = $this->extractAddress();
                $addresses = $address === null ? [] : [$address];

                $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
                $customer->setAddresses($addresses);

                if ($this->isCustomerAccount) {
                    $this->validateNavisionId($navCustomerId);
                    $password = $this->getRequest()->getParam('password');
                    $confirmation = $this->getRequest()->getParam('password_confirmation');

                    $this->checkPasswordConfirmation($password, $confirmation);
                } elseif ($this->isCompanyAccount) {
                    $password = $this->randomPassword();
                }
                
                $redirectUrl = $this->session->getBeforeAuthUrl();
                
                $customer = $this->accountManagement
                    ->createAccount($customer, $password, $redirectUrl);

                if ($this->getRequest()->getParam('is_subscribed', false)) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
                }

                $this->_eventManager->dispatch(
                    'customer_register_success',
                    ['account_controller' => $this, 'customer' => $customer]
                );

                if ($this->isCustomerAccount) {
                    $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
                    if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                        $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                        // @codingStandardsIgnoreStart
                        $this->messageManager->addSuccess(
                            __('confirmationEmailMessage',$email)
                        );
                        // @codingStandardsIgnoreEnd
                        $url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
                        $resultRedirect->setUrl($this->_redirect->success($url));
                    } else {
                        $this->messageManager->addSuccess($this->getSuccessMessage());

                        $this->session->setCustomerDataAsLoggedIn($customer);
                        $requestedRedirect = $this->accountRedirect->getRedirectCookie();
                        if (!$this->scopeConfig->getValue('customer/startup/redirect_dashboard') &&
                            $requestedRedirect) {
                            $resultRedirect->setUrl($this->_redirect->success($requestedRedirect));
                            $this->accountRedirect->clearRedirectCookie();
                            return $resultRedirect;
                        }
                        $resultRedirect = $this->accountRedirect->getRedirect();
                    }
                } elseif ($this->isCompanyAccount) {
                        $this->messageManager->addSuccess($this->getSuccessMessage());
                        $url = $this->urlModel->getUrl('/', ['_secure' => true]);
                        $resultRedirect->setUrl($this->_redirect->success($url));
                }

                if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                    $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                }

                return $resultRedirect;
            } catch (StateException $e) {
                $url = $this->urlModel->getUrl('customer/account/forgotpassword');
                // @codingStandardsIgnoreStart
                $message = __('confirmationEmailAlreadySendMessage',$url);
                // @codingStandardsIgnoreEnd
                $this->messageManager->addError($message);
            } catch (InputException $e) {
                $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            } catch (\Exception $e) {
                if ($this->isCustomerAccount) {
                    $this->messageManager->addException($e, __('We can\'t save the customer.'));
                } elseif ($this->isCompanyAccount) {
                    $this->messageManager->addException($e, __('We can\'t save the company.'));
                }

            }
        }
        
        $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        return $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
    }

    /**
     * Generate random password
     *
     * @return string
     */
    public function randomPassword()
    {
        $chars = $this->mathRandom->getRandomString(5, Random::CHARS_LOWERS) .
            $this->mathRandom->getRandomString(5, Random::CHARS_UPPERS) .
            $this->mathRandom->getRandomString(5, Random::CHARS_DIGITS);

        return str_shuffle($chars);
    }
    
    /**
     * File validation
     *
     * @param string $fieldName
     * @return string
     */
    public function validateResellerCertificate($fieldName)
    {
        $validationStatus = true;
        
        $allowedFileType = $this->registerViewModel
            ->getScopeConfigValue('reseller_certification/general/supported_file');
        $allowedMaxFileSize = $this->registerViewModel
            ->getScopeConfigValue('reseller_certification/general/max_filesize_limit');
        
        try {
            $uploader = $this->uploaderFactory->create(['fileId'=>$fieldName]);
            $uploader->setAllowedExtensions(array_map('trim', explode(',', $allowedFileType)));
            
            if ($uploader->getFileSize() > $this->registerViewModel->convertMBtoBytes($allowedMaxFileSize)) {
                $validationStatus = false;
                $this->messageManager->addErrorMessage(__("uploadFileTooLarge", $allowedMaxFileSize));
            }

            if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
                $validationStatus = false;
                $this->messageManager->addErrorMessage(__("uploadFileExtensionInvalid", strtoupper($allowedFileType)));
            }
        } catch (\Exception $ex) {
            $validationStatus = false;
            $this->messageManager->addErrorMessage(__("uploadFileOtherError", $allowedMaxFileSize, strtoupper($allowedFileType)));
        }
        
        return $validationStatus;
    }

    /**
     *
     * @param string $navId
     * @return boolean
     * @throws LocalizedException
     */
    public function validateNavisionId($navId)
    {
        $customer = $this->customerFactory->create()
            ->getCollection()
            ->addFieldToFilter('nav_customer_id', $navId)
            ->addFieldToFilter('account_type','company')
            ->addFieldToFilter('is_certificate_approved','1');
        $companyAccountData = $customer->getData();

        if (!isset($companyAccountData[0]['nav_customer_id'])) {

            throw new LocalizedException(
                new Phrase("noCompanyAssociatedWithNavId = '%1'", [$navId])
            );
        }
        return true;
    }
}
