<?php

namespace Creatuity\Nav\Model\Task;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Creatuity\Nav\Model\Data\Manager\Nav\DataManager;
use Creatuity\Nav\Model\Data\Processor\Manager\DataProcessorManager;
use Creatuity\Nav\Model\Map\CollectionMap;
use Creatuity\Nav\Model\Service\Request\Dimension\MultipleDimension;
use Creatuity\Nav\Model\Service\Request\Operation\ReadOperation;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\FilterGroup;
use Creatuity\Nav\Model\Service\Request\Parameters\Filter\MultipleValueFilter;
use Creatuity\Nav\Model\Service\Request\Parameters\FilterParameters;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Service;
use Creatuity\Nav\Model\Task\Data\Filter\NumericSkuFilterFactory;

class ProductUpdateTask implements TaskInterface
{
    protected $navFilterField;
    protected $dataObjectFactory;
    protected $dataProcessorManager;
    protected $collectionMap;
    protected $navProductDataManager;
    protected $itemService;
    protected $logger;
    protected $numericSkuFilterFactory;

    public function __construct(
        $navFilterField,
        DataObjectFactory $dataObjectFactory,
        DataProcessorManager $dataProcessorManager,
        CollectionMap $collectionMap,
        DataManager $navProductDataManager,
        Service $itemService,
        LoggerInterface $logger,
        NumericSkuFilterFactory $numericSkuFilterFactory
    ) {
        $this->navFilterField = $navFilterField;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataProcessorManager = $dataProcessorManager;
        $this->collectionMap = $collectionMap;
        $this->navProductDataManager = $navProductDataManager;
        $this->itemService = $itemService;
        $this->logger = $logger;
        $this->numericSkuFilterFactory = $numericSkuFilterFactory;
    }

    public function execute()
    {
        $this->updateProducts();
    }

    protected function updateProducts()
    {
        $iterCount = 0;

        foreach ($this->collectionMap->getPageIndices() as $pageIndex) {
            $this->collectionMap->setPage($pageIndex);


            try {
                $navProducts = $this->fetchNavProducts($this->collectionMap->getKeys());
            } catch (Exception $exception) {
                $this->logger->error($exception);
                $this->logger->debug(
                    "\nNAV Product Update: Failed to fetch NAV product data for page {$pageIndex} / {$this->collectionMap->getPageCount()}. Skipping."
                );
                continue;
            }


            $failedCount = 0;
            $updatedCount = 0;

            foreach ($navProducts as $navProductData) {
                $updateResult = $this->updateProduct($navProductData);

                if ($updateResult) {
                    ++$updatedCount;
                } else {
                    ++$failedCount;
                }
            }

            ++$iterCount;

            $this->logger->debug(
                "\nNAV Product Update:\nProcessed {$iterCount} / {$this->collectionMap->getPageCount()} pages.\nSuccessfully updated {$updatedCount} records.\nFailed to update {$failedCount} records."
            );
        }
    }

    protected function updateProduct(array $navProductData)
    {
        try {
            $navProductData = $this->navProductDataManager->process($navProductData);

            $intermediateData = $this->dataObjectFactory->create($navProductData);

            $productData = $this->collectionMap->get($intermediateData->getSku());

            $this->dataProcessorManager->process($productData, $intermediateData);
        } catch (Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }

    protected function fetchNavProducts(array $skus)
    {
        return $this->itemService->process(
            new ServiceRequest(
                new ReadOperation(),
                new MultipleDimension(),
                new FilterParameters(
                    new FilterGroup([
                        new MultipleValueFilter(
                            $this->navFilterField,
                            $this->numericSkuFilterFactory
                                ->create([
                                    'skus' => $skus,
                                ])
                                ->filter()
                        ),
                    ])
                )
            )
        );
    }
}
