<?php

namespace Creatuity\Nav\Model\Provider\Nav;

use Magento\Sales\Api\Data\OrderInterface;
use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Service\Request\Dimension\SingleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\CreateOperation;
use Creatuity\Nav\Model\Service\Request\Operation\UpdateOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\EntityParametersFactory;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\EmailFilter;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Task\ConflictResolver\EntityConflictResolverInterface;
use Creatuity\Nav\Model\Data\Manager\Magento\OrderDataManager;

class CustomerProvider
{
    protected $customerService;
    protected $findCustomerOrderDataManager;
    protected $createCustomerOrderDataManager;
    protected $entityParametersFactory;
    protected $entityConflictResolver;
    protected $fieldDataExtractorMappings;

    public function __construct(
        Service $customerService,
        OrderDataManager $findCustomerOrderDataManager,
        OrderDataManager $createCustomerOrderDataManager,
        EntityParametersFactory $entityParametersFactory,
        EntityConflictResolverInterface $entityConflictResolver,
        array $fieldDataExtractorMappings
    ) {
        $this->customerService = $customerService;
        $this->findCustomerOrderDataManager = $findCustomerOrderDataManager;
        $this->createCustomerOrderDataManager = $createCustomerOrderDataManager;
        $this->entityParametersFactory = $entityParametersFactory;
        $this->entityConflictResolver = $entityConflictResolver;
        $this->fieldDataExtractorMappings = $fieldDataExtractorMappings;
    }

    public function get(OrderInterface $order)
    {
        $customers = $this->getExistingCustomers($order);

        if (empty($customers)) {
            return $this->createCustomer($order);
        }

        return $this->updateCustomer(
            $order,
            $this->entityConflictResolver->resolve($customers)
        );
    }

    protected function getExistingCustomers(OrderInterface $order)
    {
        $findCustomerQueryData = $this->findCustomerOrderDataManager->process($order);
        if (count($findCustomerQueryData) !== 1) {
            throw new \Exception("Customer query data should not contain more than one email field");
        }

        $filters = [];
        foreach ($findCustomerQueryData as $field => $value) {
            $filters[] = new EmailFilter($field, $value);
        }

        return $this->customerService->process(
            new ServiceRequest(
                new ReadOperation(),
                new MultipleDimension(),
                new FilterParameters(
                    new FilterGroup($filters)
                )
            )
        );
    }

    protected function createCustomer(OrderInterface $order)
    {
        return $this->customerService->process(
            new ServiceRequest(
                new CreateOperation(),
                new SingleDimension(),
                $this->entityParametersFactory->create(
                    $this->createCustomerOrderDataManager->process($order)
                )
            )
        );
    }

    protected function updateCustomer(OrderInterface $order, array $customer)
    {
        $data = [];
        foreach ($this->fieldDataExtractorMappings as $dataExtractorMapping) {
            $data = array_merge($data, $dataExtractorMapping->apply($customer));
        }
        $data = array_merge($data, $this->createCustomerOrderDataManager->process($order));

        $parameters = $this->entityParametersFactory->create($data);

        return $this->customerService->process(
            new ServiceRequest(
                new UpdateOperation(),
                new SingleDimension(),
                $parameters
            )
        );
    }
}