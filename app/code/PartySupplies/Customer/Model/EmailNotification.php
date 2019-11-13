<?php

namespace PartySupplies\Customer\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Store\Model\ScopeInterface;

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailNotification extends \Magento\Customer\Model\EmailNotification
{
    
    /**#@-*/
    private $customerRegistry;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;
    
    const NEW_COMPANYACCOUNT_EMAIL_REGISTERED = 'company_registered';
    
    const XML_PATH_REGISTER_COMPANY_EMAIL_TEMPLATE = 'customer/create_account/company_email_template';
    
    const XML_PATH_REGISTER_EMAIL_CC = 'customer/create_account/account_registration_add_cc';

    const TEMPLATE_TYPES = [
        self::NEW_ACCOUNT_EMAIL_REGISTERED => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,
        self::NEW_COMPANYACCOUNT_EMAIL_REGISTERED => self::XML_PATH_REGISTER_COMPANY_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD => self::XML_PATH_REGISTER_NO_PASSWORD_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_CONFIRMED => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE,
        self::NEW_ACCOUNT_EMAIL_CONFIRMATION => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
    ];
    
    /**
     * @param CustomerRegistry $customerRegistry
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param DataObjectProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface|null $senderResolver
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        CustomerViewHelper $customerViewHelper,
        DataObjectProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver = null
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver ?: ObjectManager::getInstance()->get(SenderResolverInterface::class);
        
        parent::__construct(
            $customerRegistry,
            $storeManager,
            $transportBuilder,
            $customerViewHelper,
            $dataProcessor,
            $scopeConfig,
            $senderResolver
        );
    }
    
     /**
      * Create an object with data merged from Customer and CustomerSecure
      *
      * @param CustomerInterface $customer
      * @return \Magento\Customer\Model\Data\CustomerSecure
      */
    private function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }
    
    /**
     * Send email with new account related information
     *
     * @param CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @param string $sendemailStoreId
     * @return void
     * @throws LocalizedException
     */
    public function newAccount(
        CustomerInterface $customer,
        $type = self::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {
        $isCompanyAccount = $customer->getCustomAttribute('account_type')->getValue() === "company";
       
        if ($isCompanyAccount && isset(self::TEMPLATE_TYPES[$type])) {
            $type = self::NEW_COMPANYACCOUNT_EMAIL_REGISTERED;
        }
        
        $types = self::TEMPLATE_TYPES;
            
        if (!isset($types[$type])) {
            throw new LocalizedException(
                __('The transactional account email type is incorrect. Verify and try again.')
            );
        }
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
        }

        $store = $this->storeManager->getStore($customer->getStoreId());

        $customerEmailData = $this->getFullCustomerObject($customer);

        $this->sendEmailTemplate(
            $customer,
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            ['customer' => $customerEmailData, 'back_url' => $backUrl, ScopeInterface::SCOPE_STORE => $store],
            $storeId
        );
    }
    
    /**
     * Send corresponding email template
     *
     * @param CustomerInterface $customer
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $email
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    private function sendEmailTemplate(
        $customer,
        $template,
        $sender,
        $templateParams = [],
        $storeId = null,
        $email = null
    ) {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }

        /** @var array $from */
        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId),
            $storeId
        );

        $emailCcTo = explode(
            ',',
            $this->scopeConfig->getValue(self::XML_PATH_REGISTER_EMAIL_CC, ScopeInterface::SCOPE_STORE, $storeId)
        );

        if (count($emailCcTo) > 1) {
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', ScopeInterface::SCOPE_STORE => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($from)
                ->addTo($email, $this->customerViewHelper->getCustomerName($customer))
                ->addCc($emailCcTo)
                ->getTransport();
        } else {
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', ScopeInterface::SCOPE_STORE => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($from)
                ->addTo($email, $this->customerViewHelper->getCustomerName($customer))
                ->getTransport();
        }

        $transport->sendMessage();
    }
}
