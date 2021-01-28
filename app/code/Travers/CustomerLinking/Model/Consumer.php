<?php
declare(strict_types=1);

namespace Travers\CustomerLinking\Model;

use Travers\CustomerLinking\Model\CustomerSync;
use Travers\CustomerLinking\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;

class Consumer
{
    public function __construct(
        CustomerSync $customerApi,
        Data $data,
        Customer $customerModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->customerApi = $customerApi;
        $this->helper = $data;
        $this->customer = $customerModel;
        $this->date = $date;
    }

    public function consumeCustomerId(String $customerId) {
        $count = (int)$this->helper->getConfigValue('sx/customer_linking/retry_count');
        $today = $this->date->gmtDate();
        try {
            $this->helper->logData("Customer account sync started for Magento customer id : ".$customerId);
            while($count>0) {
                $customer = $this->customer->load($customerId);
                $response = $this->customerApi->postCustomerSync($customerId);
                $message = $response['body']['Message'];
                $customer->setLastAccountLinkingDate($today);
                $customer->setLastAccountLinkingMessage($message);
                $customer->save();
                if(isset($response['status']) && $response['status'] == 500) {
                    $count--;
                    $this->helper->logData("Customer account linking failed for customer id ".$customerId." with message ".$message." and Retry triggered. Remaining Retries ".$count);
                }
                else if(isset($response['status']) && $response['status'] == 200) {
                    $this->helper->logData("Customer account linking for customer id ".$customerId." Success with message ".$message);
                    $count = 0;
                }
                else {
                    $this->helper->logData("Customer account linking failed for customer id ".$customerId." with message '".$message."'");
                    $count = 0;
                    $this->helper->sendMail($message, $customerId);
                }
            }
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }
    }
}