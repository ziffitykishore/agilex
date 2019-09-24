<?php

/**
 * Customer creation in NAVISION
 */
namespace Creatuity\Nav\Model\Provider\Nav;

use Exception;
use Psr\Log\LoggerInterface;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Request\Dimension\SingleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\CreateOperation;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Operation\UpdateOperation;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Parameters\EntityParametersFactory;
use Creatuity\Nav\Model\Data\Manager\Magento\CustomerDataManager;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\SingleValueFilter;

/**
 * CustomerApproval
 */
class CustomerApproval
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Service
     */
    protected $customerService;
    
    /**
     * @var EntityParametersFactory
     */
    protected $entityParametersFactory;

    /**
     * @var CustomerDataManager
     */
    protected $createCustomerDataManager;
    
    /**
     * @var CustomerDataManager
     */
    protected $updateCustomerDataManager;
    
    /**
     * @var CustomerDataManager
     */
    protected $findCustomerDataManager;

    /**
     * 
     * @param LoggerInterface         $logger 
     * @param Service                 $customerService 
     * @param CustomerDataManager     $createCustomerDataManager 
     * @param CustomerDataManager     $updateCustomerDataManager 
     * @param CustomerDataManager     $findCustomerDataManager 
     * @param EntityParametersFactory $entityParametersFactory 
     */
    public function __construct(
        LoggerInterface $logger,
        Service $customerService,
        CustomerDataManager $createCustomerDataManager,
        CustomerDataManager $updateCustomerDataManager,
        CustomerDataManager $findCustomerDataManager,
        EntityParametersFactory $entityParametersFactory
    ) {
        $this->logger = $logger;
        $this->customerService = $customerService;
        $this->createCustomerDataManager = $createCustomerDataManager;
        $this->updateCustomerDataManager = $updateCustomerDataManager;
        $this->findCustomerDataManager   = $findCustomerDataManager;
        $this->entityParametersFactory = $entityParametersFactory;
    }    
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return array 
     */
    public function createCustomer(array $customerData)
    {   
        try {
            $result = $this->customerService->process(
                new ServiceRequest(
                    new CreateOperation(),
                    new SingleDimension(),
                    $this->entityParametersFactory->create(
                        $this->createCustomerDataManager->process($customerData)
                    )
                )
            );
            $this->logger->info('Company Account Created Sucessfully');
            $this->logger->info('NAVISION Customer ID = '.$result['No']);
            return $result;
            
        } catch (Exception $ex) {
            $this->logger->error($ex);
        }
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return array 
     */
    public function getExistingCustomer(array $customerData)
    {
        $findCustomerQueryData = $this->findCustomerDataManager->process(
            $customerData
        );
        $filters = [];
        foreach ($findCustomerQueryData as $field => $value) {
            $filters[] = new SingleValueFilter($field, $value);
        }
        
        try {
            $result = $this->customerService->process(
                new ServiceRequest(
                    new ReadOperation(),
                    new MultipleDimension(),
                    new FilterParameters(
                        new FilterGroup($filters)
                    )
                )
            );
            $this->logger->info('Customer Retrived From NAVISON');
            
            return $result;
            
        } catch (Exception $ex) {
            $this->logger->error($ex);
        }
    }
    
    /**
     * 
     * @param array $customerData 
     * 
     * @return array 
     */
    public function updateCustomer(array $customerData)
    {   
        try {
            $result = $this->customerService->process(
                new ServiceRequest(
                    new UpdateOperation(),
                    new SingleDimension(),
                    $this->entityParametersFactory->create(
                        $this->updateCustomerDataManager->process($customerData)
                    )
                )
            );
            $this->logger->info('Customer Address Updated Successfully');
            
            return $result;
            
        } catch (Exception $ex) {
            $this->logger->error($ex);
        }
    }
}
