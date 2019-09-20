<?php

/**
 * Customer save after action
 */
namespace Creatuity\Nav\Plugin;

use Creatuity\Nav\Model\Provider\Nav\CustomerApproval;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * CustomerSaveAfter
 */
class CustomerSaveAfter
{
    const COMPANY = 'company';
    
    /**
     *
     * @var CustomerApproval
     */
    protected $customerApproval;
    
    /**
     *
     * @var Customer
     */
    protected $customer;

    /**
     *
     * @var CustomerFactory 
     */
    protected $customerFactory;

    /**
     * 
     * @param CustomerApproval $customerApproval 
     * @param Customer         $customer 
     * @param CustomerFactory  $customerFactory 
     */
    public function __construct(
        CustomerApproval $customerApproval,
        Customer $customer,
        CustomerFactory $customerFactory            
    ) {
        $this->customerApproval = $customerApproval;    
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;        
    }

    /**
     * 
     * @param \Magento\Customer\Controller\Adminhtml\Index\Save $subject 
     * @param PageFactory                                       $result 
     * 
     * @return PageFactory
     */
    public function afterExecute(
        \Magento\Customer\Controller\Adminhtml\Index\Save $subject,
        $result
    ) {
        $customerData = $subject->getRequest()->getPostValue();
        if (
            $customerData['customer']['account_type'] === self::COMPANY
            && $customerData['customer']['is_certificate_approved']
            && !$customerData['customer']['nav_customer_id']
        ) {
            $navCustomerId = $this->customerApproval->createCustomer($customerData);
            $customerUpdated = $this->saveNavCustomerId(
                $customerData['customer']['entity_id'],
                $navCustomerId['No']
            );
            
            if ($customerUpdated) {
                $navCustomer = $this->customerApproval->getExistingCustomer(
                    $navCustomerId
                );
                $customerData['Key'] = $navCustomer[0]['Key'];
            }

            $this->customerApproval->updateCustomer($customerData);
        }
        
        return $result;
    }
    
    /**
     * 
     * @param string $customerId 
     * @param string $navCustomerId 
     * 
     * @return boolean 
     */
    public function saveNavCustomerId(string $customerId, string $navCustomerId)
    {
        $customer = $this->customer->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('nav_customer_id', $navCustomerId);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, 'nav_customer_id');
        
        return true;
    }
}
