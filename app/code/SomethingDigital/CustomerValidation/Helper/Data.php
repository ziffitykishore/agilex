<?php

namespace SomethingDigital\CustomerValidation\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\CustomerFactory;
use SomethingDigital\CustomerValidation\Model\CustomerApi;
use Psr\Log\LoggerInterface;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $customerFactory;
    private $customerApi;
    protected $logger;

    public function __construct(
    	Context $context,
        CustomerFactory $customerFactory,
        CustomerApi $customerApi,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->customerApi = $customerApi;
        $this->logger = $logger;
    }

    public function isCustomerRegistered($traversAccountId) {
        $customerCollection = $this->customerFactory->create()->getCollection()
            ->addAttributeToFilter('travers_account_id', $traversAccountId)
            ->load();
        foreach ($customerCollection as $customer) {
            return true;
        }
        return false;
    }

    public function isZipCodeValid($traversAccountId, $accountZipCode) {
        try {
            $customerApi = $this->customerApi->getCustomer($traversAccountId);
            if ($customerApi['body']['Address']['PostalCode'] == $accountZipCode) {
                return true;
            }
        } catch (Exception $e) {
            $this->logger->alert($e);
        }
        return false;
    }
}
