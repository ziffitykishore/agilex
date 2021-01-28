<?php

namespace SomethingDigital\CustomerValidation\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\CustomerFactory;
use SomethingDigital\CustomerValidation\Model\CustomerApi;
use Psr\Log\LoggerInterface;
 
class Validation extends \Magento\Framework\App\Helper\AbstractHelper
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

        if ($customerCollection->getSize()) {
            return true;
        }

        return false;
    }

    public function isZipCodeValid($traversAccountId, $accountZipCode) {
        try {
            $customerApi = $this->customerApi->getCustomer($traversAccountId);
            if ($customerApi && $customerApi['body']['Address']['PostalCode'] == $accountZipCode) {
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }
        return false;
    }

    public function validate($traversAccountId, $accountZipCode) {
        $message = '';
        if(!empty($traversAccountId)) {
            $this->logData("Pre Auth process started with Travers Account Id : ".$traversAccountId." and Zip code : ".$accountZipCode);
            if (empty($accountZipCode)) {
                $message = __(
                    'Account Zip Code field can not be empty.'
                );
            } elseif ($this->isCustomerRegistered($traversAccountId)) {
                    $message = __(
                        'There is already an account with this account number.'
                    );
            } elseif (!$this->isZipCodeValid($traversAccountId, $accountZipCode)) {
                $message = __(
                    'Zip Code doesn\'t match customer number.'
                );
            }
        }
        if($message != '')
            $this->logData($message->getText().' '.$traversAccountId);
        else
        $this->logData('Pre Auth success for Account Id '.$traversAccountId);
        return $message;
    }

    private function logData($message = null, $enable = false)
    {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pre_auth.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($message, true));
    }
}
