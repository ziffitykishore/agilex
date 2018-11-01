<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Company\Api\CompanyRepositoryInterface;

$user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\User\Model\User::class);
$user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\Customer\Model\Customer::class);
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(1)
    ->setId(1)
    ->setEmail('email@companyquote.com')
    ->setPassword('password')
    ->setFirstname('John')
    ->setLastname('Smith');
$customer->isObjectNew(true);
$customer->save();

$companyRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(CompanyRepositoryInterface::class);
/** @var CompanyInterface $company */
$company = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(CompanyInterface::class);
$company->setCompanyName('company quote');
$company->setStatus(CompanyInterface::STATUS_APPROVED);
$company->setCompanyEmail('email@companyquote.com');
$company->setComment('comment');
$company->setSuperUserId($customer->getId());
$company->setCustomerGroupId(1);
$company->setSalesRepresentativeId($user->getId());
$company->setCountryId('TV');
$company->setCity('City');
$company->setStreet(['avenue, 30']);
$company->setPostcode('postcode');
$company->setTelephone('123456');
$companyRepository->save($company);
$company = $companyRepository->get($company->getId());
$company->getExtensionAttributes()->getQuoteConfig()->setIsQuoteEnabled(true);
$companyRepository->save($company);

/** @var $creditLimit \Magento\CompanyCredit\Model\CreditLimit */
$creditLimitManagement = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\CompanyCredit\Model\CreditLimitManagement::class);
$creditLimit = $creditLimitManagement->getCreditByCompanyId($company->getId());
$creditLimit->setCreditLimit(100);
$creditLimit->setBalance(-50);
$creditLimit->save();
