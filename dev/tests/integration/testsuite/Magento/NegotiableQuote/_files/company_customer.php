<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require 'company_with_customer_for_quote.php';

/** @var $repository \Magento\Customer\Api\CustomerRepositoryInterface */
$bootstrap = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$repository = $bootstrap->create(\Magento\Customer\Api\CustomerRepositoryInterface::class);
$extensionFactory = $bootstrap->create(\Magento\Framework\Api\ExtensionAttributesFactory::class);
$customerExtension = $extensionFactory->create(\Magento\Customer\Api\Data\CustomerInterface::class);
$companyCustomerAttributes = $bootstrap->create(\Magento\Company\Api\Data\CompanyCustomerInterfaceFactory::class);
$companyAttributes = $companyCustomerAttributes->create();
$customerCompany = $bootstrap->create(\Magento\Customer\Model\Customer::class);
$customerCompany->setWebsiteId(1)
    ->setId(22)
    ->setEmail('customercompany22@example.com')
    ->setFirstname('John')
    ->setLastname('Smith');
$customerCompany->isObjectNew(true);
$customerCompany->save();
$customerCompany = $repository->get('customercompany22@example.com');
if ($customerCompany->getExtensionAttributes() === null) {
    $customerCompany->setExtensionAttributes($customerExtension);
}
if ($customerCompany->getExtensionAttributes()->getCompanyAttributes() === null) {
    $customerCompany->getExtensionAttributes()->setCompanyAttributes($companyAttributes);
}
$repository->save($customerCompany);
$customerCompany = $repository->get('customercompany22@example.com');

/** @var \Magento\Company\Api\CompanyManagementInterface $companyManagement */
$companyManagement = $bootstrap->create(\Magento\Company\Api\CompanyManagementInterface::class);
$companyManagement->assignCustomer($company->getId(), $customerCompany->getId());
