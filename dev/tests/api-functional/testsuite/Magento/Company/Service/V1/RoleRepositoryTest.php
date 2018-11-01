<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Company\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test Role CRUD operations.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RoleRepositoryTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'companyRoleRepositoryV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @var \Magento\Company\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->customerRepository = $this->objectManager->get(
            \Magento\Customer\Api\CustomerRepositoryInterface::class
        );
        $this->companyManagement = $this->objectManager->get(
            \Magento\Company\Api\CompanyManagementInterface::class
        );
        $this->roleManagement = $this->objectManager->get(
            \Magento\Company\Api\RoleManagementInterface::class
        );
        $this->dataObjectProcessor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\Reflection\DataObjectProcessor::class
        );
    }

    /**
     * Test role get via WebAPI.
     *
     * @return void
     * @magentoApiDataFixture Magento/NegotiableQuote/_files/company_with_customer_for_quote.php
     */
    public function testGetRole()
    {
        $customer = $this->customerRepository->get('email@companyquote.com');
        $company = $this->companyManagement->getByCustomerId($customer->getId());
        $role = $this->roleManagement->getCompanyDefaultRole($company->getId());
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/company/role/' . $role->getId(),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'Get',
            ],
        ];
        $requestData = ['roleId' => $role->getId()];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($response['id'], $role->getId());
        $this->assertEquals($response['role_name'], $role->getRoleName());
    }

    /**
     * Test role get list via WebAPI.
     *
     * @return void
     * @magentoApiDataFixture Magento/NegotiableQuote/_files/company_with_customer_for_quote.php
     */
    public function testGetRoleList()
    {
        $customer = $this->customerRepository->get('email@companyquote.com');
        $company = $this->companyManagement->getByCustomerId($customer->getId());
        $role = $this->roleManagement->getCompanyDefaultRole($company->getId());

        $builder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Api\FilterBuilder::class);

        $filter = $builder
            ->setField(\Magento\Company\Api\Data\RoleInterface::ROLE_ID)
            ->setValue($role->getId())
            ->create();

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\Api\SearchCriteriaBuilder::class
        );

        $searchCriteriaBuilder->addFilters([$filter]);
        $searchCriteriaBuilder->setCurrentPage(1);
        $searchCriteriaBuilder->setPageSize(20);
        $searchData = $this->dataObjectProcessor->buildOutputDataArray(
            $searchCriteriaBuilder->create(),
            \Magento\Framework\Api\SearchCriteriaInterface::class
        );
        $requestData = ['searchCriteria' => $searchData];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/company/role/' . '?' . http_build_query($requestData),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'GetList',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($response['items'][0]['id'], $role->getId());
        $this->assertEquals($response['items'][0]['role_name'], $role->getRoleName());
    }

    /**
     * Test role creation via WebAPI.
     *
     * @return void
     * @magentoApiDataFixture Magento/NegotiableQuote/_files/company_with_customer_for_quote.php
     */
    public function testCreateRole()
    {
        $customer = $this->customerRepository->get('email@companyquote.com');
        $company = $this->companyManagement->getByCustomerId($customer->getId());

        $roleFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Company\Api\Data\RoleInterfaceFactory::class);

        $role = $roleFactory->create();
        $role->setRoleName('New Role');
        $role->setCompanyId($company->getId());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/company/role/',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'Save',
            ],
        ];

        $roleDataObject = $this->dataObjectProcessor->buildOutputDataArray(
            $role,
            \Magento\Company\Api\Data\RoleInterface::class
        );
        $requestData = ['role' => $roleDataObject];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($response['role_name'], $role->getRoleName());
    }

    /**
     * Test role update via WebAPI.
     *
     * @return void
     * @magentoApiDataFixture Magento/NegotiableQuote/_files/company_with_customer_for_quote.php
     */
    public function testUpdateRole()
    {
        $customer = $this->customerRepository->get('email@companyquote.com');
        $company = $this->companyManagement->getByCustomerId($customer->getId());
        $role = $this->roleManagement->getCompanyDefaultRole($company->getId());
        $role->setRoleName('Updated Role');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf('/V1/company/role/%d', $role->getId()),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'Save',
            ],
        ];

        $roleDataObject = $this->dataObjectProcessor->buildOutputDataArray(
            $role,
            \Magento\Company\Api\Data\RoleInterface::class
        );
        $requestData = ['role' => $roleDataObject];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($response['id'], $role->getId());
        $this->assertEquals($response['role_name'], $role->getRoleName());
    }

    /**
     * Test role delete via WebAPI.
     *
     * @return void
     * @magentoApiDataFixture Magento/NegotiableQuote/_files/company_with_customer_for_quote.php
     */
    public function testDeleteRole()
    {
        $customer = $this->customerRepository->get('email@companyquote.com');
        $company = $this->companyManagement->getByCustomerId($customer->getId());

        $roleFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Company\Api\Data\RoleInterfaceFactory::class);
        /** @var \Magento\Company\Api\RoleRepositoryInterface $roleRepository */
        $roleRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Company\Api\RoleRepositoryInterface::class);

        $role = $roleFactory->create();
        $role->setRoleName('New Role');
        $role->setCompanyId($company->getId());
        $roleRepository->save($role);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/company/role/' . $role->getId(),
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'Delete',
            ],
        ];
        $requestData = ['roleId' => $role->getId()];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($response, 1);
    }
}
