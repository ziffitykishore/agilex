<?php

namespace Travers\CustomerLinking\Model;

use SomethingDigital\Sx\Model\Adapter;

class CustomerSync extends Adapter
{

    /**
     * @param int $orderId
     * @return array
     * @throws LocalizedException
     */
    public function postCustomerSync($customerId)
    {
        if ($customerId) {
            $this->requestPath = 'api/Customer/Sync/' . $customerId;

            return $this->postRequest();
        } else {
            return [];
        }
    }

}
