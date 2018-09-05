<?php

namespace Ziffity\Webforms\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Ziffity\Webforms\Api\CatalogRepositoryInterface;
use Ziffity\Webforms\Api\Catalog\CatalogInterface;
use Ziffity\Webforms\Api\Catalog\CatalogInterfaceFactory;
use Ziffity\Webforms\Api\Catalog\CatalogSearchResultsInterfaceFactory;
use Ziffity\Webforms\Model\ResourceModel\Catalog as ResourceData;
use Ziffity\Webforms\Model\ResourceModel\Catalog\CollectionFactory as DataCollectionFactory;

class CatalogRepository implements CatalogRepositoryInterface
{
    protected $instances = [];

    protected $resource;

    protected $dataCollectionFactory;

    protected $searchResultsFactory;

    protected $dataInterfaceFactory;

    protected $dataObjectHelper;

    public function __construct(
        ResourceData $resource,
        DataCollectionFactory $dataCollectionFactory,
        CatalogSearchResultsInterfaceFactory $dataSearchResultsInterfaceFactory,
        CatalogInterfaceFactory $dataInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->searchResultsFactory = $dataSearchResultsInterfaceFactory;
        $this->dataInterfaceFactory = $dataInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function save(CatalogInterface $data)
    {
        try {

            $this->resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    public function getById($dataId)
    {
        if (!isset($this->instances[$dataId])) {
            
            $data = $this->dataInterfaceFactory->create();
            $this->resource->load($data, $dataId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }
            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);


        $collection = $this->dataCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = 'customer_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $data = [];
        foreach ($collection as $datum) {
            $dataDataObject = $this->dataInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($dataDataObject, $datum->getData(), CoinInterface::class);
            $data[] = $dataDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($data);
    }

    public function delete(CatalogInterface $data)
    {

        $id = $data->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    public function deleteById($dataId)
    {
        $data = $this->getById($dataId);
        return $this->delete($data);
    }
}
