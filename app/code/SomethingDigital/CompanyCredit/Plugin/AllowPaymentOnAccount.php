<?php

namespace SomethingDigital\CompanyCredit\Plugin;

use Magento\Company\Model\PermissionManagement;

class AllowPaymentOnAccount
{
    /**
     * Allow payment on account on the default company rule
     */
    public function afterRetrieveAllowedResources(PermissionManagement $subject, $result)
    {   
        $result[] = 'Magento_Sales::payment_account';
        return $result;
    }
}
