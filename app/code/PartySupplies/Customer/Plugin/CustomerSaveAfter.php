<?php

/**
 * Customer save after action
 */
namespace PartySupplies\Customer\Plugin;

use Magento\Customer\Controller\Adminhtml\Index\Save;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Customer\Model\EmailNotification;
use Magento\Framework\Mail\Template\TransportBuilder;
use Creatuity\Nav\Model\Provider\Nav\CustomerApproval;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface;
use PartySupplies\Customer\Helper\Constant;

/**
 * CustomerSaveAfter
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerSaveAfter
{

    /**
     * @var CustomerApproval
     */
    protected $customerApproval;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;
    
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    
    /**
     * @var ManagerInterface;
     */
    protected $messageManager;

    /**
     *
     * @param ScopeConfigInterface    $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param TransportBuilder        $transportBuilder
     * @param CustomerApproval        $customerApproval
     * @param Customer                $customer
     * @param CustomerFactory         $customerFactory
     * @param ManagerInterface        $messageManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        TransportBuilder $transportBuilder,
        CustomerApproval $customerApproval,
        Customer $customer,
        CustomerFactory $customerFactory,
        ManagerInterface $messageManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->transportBuilder = $transportBuilder;
        $this->customerApproval = $customerApproval;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->messageManager = $messageManager;
    }
    
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @param Save        $subject
     * @param PageFactory $result
     *
     * @return PageFactory $result
     */
    public function afterExecute(Save $subject, $result)
    {
        $customerData = $subject->getRequest()->getPostValue();
        $returnToEdit = (bool)$subject->getRequest()->getParam('back', false);

        if ($customerData['customer']['account_type'] === Constant::COMPANY
            && $customerData['customer']['is_certificate_approved']
            && !$customerData['customer']['nav_customer_id']
        ) {
            $navCustomerId = $this->customerApproval->createCustomer($customerData);

            if ($navCustomerId) {
                $customerUpdated = $this->saveCustomerData(
                    $customerData['customer']['entity_id'],
                    'nav_customer_id',
                    $navCustomerId['No']
                );

                if ($customerUpdated) {
                    $navCustomer = $this->customerApproval->getExistingCustomer(
                        $navCustomerId
                    );
                    $customerData['Key'] = $navCustomer[0]['Key'];
                }

                $navResponse = $this->customerApproval->updateCustomer($customerData);

                if ($navResponse['No']) {
                    $mailConfig = $this->setMailConfig(
                        $customerData,
                        $customerData['customer']['is_certificate_approved']
                    );
                    $this->sendEmail($mailConfig);
                } else {
                    $this->messageManager->addError(
                        __('Something went wrong while updating address for company account in NAV.')
                    );
                    $this->saveCustomerData(
                        $customerData['customer']['entity_id'],
                        'is_certificate_approved',
                        false
                    );
                }
            } else {
                $this->messageManager->addError(
                    __('Something went wrong while creating company account in NAV.')
                );
                $this->saveCustomerData(
                    $customerData['customer']['entity_id'],
                    'is_certificate_approved',
                    false
                );
            }
        }

        if (!$returnToEdit) {
            $result->setPath('customer/index/index/account_type/'.$customerData['customer']['account_type']);
        }

        return $result;
    }

    /**
     *
     * @param string $customerId
     * @param string $attributeCode
     * @param mixed $value
     *
     * @return boolean
     */
    protected function saveCustomerData($customerId, $attributeCode, $value)
    {
        $customer = $this->customer->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute($attributeCode, $value);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, $attributeCode);

        return true;
    }

    /**
     *
     * @param string $path
     * @param string $scopeType
     * @param int    $scopeCode
     *
     * @return string
     */
    protected function getScopeConfigValue($path, $scopeType, $scopeCode)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     *
     * @param array $mailConfig
     *
     * @throws \Magento\Framework\Exception\MailException
     *
     * @return NULL
     */
    protected function sendEmail(array $mailConfig)
    {
        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($mailConfig['template_id'])
                ->setTemplateOptions(['area' => 'adminhtml', 'store' => $mailConfig['store_id']])
                ->setTemplateVars($mailConfig['template_variable'])
                ->setFrom($mailConfig['from_email_address'])
                ->addTo($mailConfig['to_email_address'], "NAVISION")
                ->getTransport();
            $transport->sendMessage();
            $this->messageManager->addSuccess(
                __('Mail sent successfully to the customer.')
            );
        } catch (\Magento\Framework\Exception\MailException $ex) {
            $this->messageManager->addException(
                $ex,
                __('Something went wrong while sending mail to the customer.')
            );
        }
    }

    /**
     *
     * @param array   $customerData
     * @param boolean $customerUpdated
     *
     * @return array
     */
    protected function setMailConfig($customerData, $customerUpdated = false)
    {
        $mailConfig = [];
        if ($customerUpdated) {
            $templateId = $this->getScopeConfigValue(
                Constant::COMPANY_APPROVED_EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $customerData['customer']['store_id']
            );
            $mailConfig['template_id'] = $templateId;
        } else {
            $templateId = $this->getScopeConfigValue(
                Constant::COMPANY_DECLINED_EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $customerData['customer']['store_id']
            );
            $mailConfig['template_id'] = $templateId;
        }

        $mailConfig['from_email_address'] = $this->senderResolver->resolve(
            $this->getScopeConfigValue(
                EmailNotification::XML_PATH_REGISTER_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $customerData['customer']['store_id']
            ),
            $customerData['customer']['store_id']
        );
        $mailConfig['to_email_address'] = $customerData['customer']['email'];
        $mailConfig['store_id'] = $customerData['customer']['store_id'];
        $mailConfig['template_variable'] = $customerData;

        return $mailConfig;
    }
}
