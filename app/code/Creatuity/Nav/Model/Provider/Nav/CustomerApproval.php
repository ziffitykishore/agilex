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
use Creatuity\Nav\Api\Data\DataInterfaceFactory;
use Creatuity\Nav\Api\DataRepositoryInterface;

/**
 * CustomerApproval
 */
class CustomerApproval
{
    const CUSTOMER_APPROVAL = 'customer_approval';

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
     * @var DataInterfaceFactory
     */
    protected $navisionLoggerFactory;
    
    /**
     * @var DataInterfaceFactory
     */
    protected $navisionLoggerRepository;

    /**
     *
     * @param LoggerInterface         $logger
     * @param Service                 $customerService
     * @param CustomerDataManager     $createCustomerDataManager
     * @param CustomerDataManager     $updateCustomerDataManager
     * @param CustomerDataManager     $findCustomerDataManager
     * @param EntityParametersFactory $entityParametersFactory
     * @param DataInterfaceFactory    $navisionLoggerFactory
     * @param DataRepositoryInterface $navisionLoggerRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Service $customerService,
        CustomerDataManager $createCustomerDataManager,
        CustomerDataManager $updateCustomerDataManager,
        CustomerDataManager $findCustomerDataManager,
        EntityParametersFactory $entityParametersFactory,
        DataInterfaceFactory $navisionLoggerFactory,
        DataRepositoryInterface $navisionLoggerRepository
    ) {
        $this->logger = $logger;
        $this->customerService = $customerService;
        $this->createCustomerDataManager = $createCustomerDataManager;
        $this->updateCustomerDataManager = $updateCustomerDataManager;
        $this->findCustomerDataManager   = $findCustomerDataManager;
        $this->entityParametersFactory = $entityParametersFactory;
        $this->navisionLoggerFactory = $navisionLoggerFactory;
        $this->navisionLoggerRepository = $navisionLoggerRepository;
    }
    
    /**
     * Create Customer
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
            $this->saveNavisionStatus(
                self::CUSTOMER_APPROVAL,
                true,
                'Company account created sucessfully '
                . 'in Navision. Nav Id = '.$result['No']
            );
            $this->logger->info('Company Account Created Sucessfully');
            $this->logger->info('NAVISION Customer ID = '.$result['No']);
            return $result;
            
        } catch (Exception $ex) {
            $this->saveNavisionStatus(
                self::CUSTOMER_APPROVAL,
                false,
                $ex->getMessage()
            );
            $this->logger->error($ex);
            return false;
        }
    }
    
    /**
     * Get Existing Customer
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
            return false;
        }
    }
    
    /**
     * Update Customer
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
            $this->saveNavisionStatus(
                self::CUSTOMER_APPROVAL,
                true,
                'Company account address updated '
                . 'sucessfully in Navision. Nav Id = '.$result['No']
            );
            $this->logger->info('Customer Address Updated Successfully');
            
            return $result;
            
        } catch (Exception $ex) {
            $this->saveNavisionStatus(
                self::CUSTOMER_APPROVAL,
                false,
                $ex->getMessage()
            );
            $this->logger->error($ex);
            return false;
        }
    }
    
    /**
     * To save navision log status
     *
     * @param string $logType
     * @param boolean $logStatus
     * @param string $description
     */
    protected function saveNavisionStatus($logType, $logStatus, $description)
    {
        $navisionData = $this->navisionLoggerFactory->create();
        $navisionData->setLogType($logType);
        $navisionData->setLogStatus($logStatus);
        $navisionData->setDescription(
            $description
        );
        $this->navisionLoggerRepository->save($navisionData);
    }
}
