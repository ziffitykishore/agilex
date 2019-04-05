<?php

namespace SomethingDigital\AlgoliaSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class AddFacets implements ObserverInterface
{
    private $collectionFactory;
    private $resource;

    public function __construct(
        CollectionFactory $collectionFactory,
        ResourceConnection $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $attrForFaceting = [];
        $transport = $observer->getEvent()->getData('index_settings');

        $indexSettings = $transport->getData();
        foreach ($indexSettings['attributesForFaceting'] as $value) {
            $attrForFaceting[] = $value;
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_filterable', true);
        $collection->setOrder('position','ASC');
    
        foreach ($collection as $item) {
            $attrForFaceting[] = 'searchable('.$item->getAttributeCode().')';
        }

        $connection = $this->resource->getConnection();
        $storeId = $observer->getEvent()->getData('store_id');
        
        $categoryFlatTable = $connection->getTableName('catalog_category_flat_store_'.$storeId);
        $groupingAttributes = $connection->fetchAll('
            SELECT DISTINCT grouping_attribute_1 FROM `'.$categoryFlatTable.'` UNION 
            SELECT DISTINCT grouping_attribute_2 FROM `'.$categoryFlatTable.'` UNION 
            SELECT DISTINCT grouping_attribute_3 FROM `'.$categoryFlatTable.'`
        ');

        foreach ($groupingAttributes as $value) {
            if ($value['grouping_attribute_1'] != NULL) {
                $attrForFaceting[] = 'searchable('.$value['grouping_attribute_1'].')';
            }
        }

        $indexSettings['attributesForFaceting'] = array_values(array_unique($attrForFaceting));

        $transport->setData($indexSettings);
    }
}