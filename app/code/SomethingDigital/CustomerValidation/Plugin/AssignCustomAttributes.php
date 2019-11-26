<?php

namespace SomethingDigital\CustomerValidation\Plugin;

use Magento\Company\Controller\Customer\Create;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;

class AssignCustomAttributes
{
    private $customerRepository;
    private $companyRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CompanyManagementInterface $companyRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->companyRepository = $companyRepository;
    }

    public function afterExecute(Create $subject, $result)
    {
        $email = $subject->getRequest()->getParam('email');
        $customer = $this->customerRepository->get($email);

        $company = $this->getCompanyByCustomerId($customer->getId());
        
        if ($company) {
            $companyOwnerId = $company->getSuperUserId();
            $parentCustomer = $this->customerRepository->getById($companyOwnerId);

            $traversAccountId = '';
            $traversAccountIdAttr = $parentCustomer->getCustomAttribute('travers_account_id');
            if (isset($traversAccountIdAttr)) {
                $traversAccountId = $traversAccountIdAttr->getValue();
            }
            if ($traversAccountId != '') {
                $customer->setCustomAttribute('travers_account_id', $traversAccountId);
                $this->customerRepository->save($customer);
            }

            $shippingRateGroup = '';
            $shippingRateGroupAttr = $parentCustomer->getCustomAttribute('shipping_rate_group');
            if (isset($shippingRateGroupAttr)) {
                $shippingRateGroup = $shippingRateGroupAttr->getValue();
            }
            if ($shippingRateGroup != '') {
                $customer->setCustomAttribute('shipping_rate_group', $shippingRateGroup);
                $this->customerRepository->save($customer);
            }
        }

        return $result;
    }

    public function getCompanyByCustomerId($customerId)
    {
        $company = $this->companyRepository->getByCustomerId($customerId);
        return $company;
    }
}
