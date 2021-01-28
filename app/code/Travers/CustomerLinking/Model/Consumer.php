<?php
declare(strict_types=1);

namespace Travers\CustomerLinking\Model;

use Travers\CustomerLinking\Model\CustomerSync;
use Travers\CustomerLinking\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Consumer
{
    public function __construct(
        CustomerSync $customerApi,
        Data $data
    ) {
        $this->customerApi = $customerApi;
        $this->helper = $data;
    }

    public function consumeCustomerId(String $customerId) {
        $count = (int)$this->helper->getConfigValue('sx/customer_linking/retry_count');
        try {
            $this->helper->logData("Customer account sync started for Magento customer id : ".$customerId);
            while($count>0) {
                $response = $this->customerApi->postCustomerSync($customerId);
                $message = $response['body']['Message'];
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
                    $this->helper->sendMail($message);
                }
            }
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage);
        }
    }
}