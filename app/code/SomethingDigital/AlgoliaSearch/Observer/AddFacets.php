<?php

namespace SomethingDigital\AlgoliaSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;

class AddFacets implements ObserverInterface
{
    private $collectionFactory;
    private $resource;
    private $categoryCollection;

    public function __construct(
        CollectionFactory $collectionFactory,
        CategoryCollection $categoryCollection
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->categoryCollection = $categoryCollection;
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

        $categories = $this->categoryCollection->create()
            ->addAttributeToSelect('grouping_attribute_1')
            ->addAttributeToSelect('grouping_attribute_2')
            ->addAttributeToSelect('grouping_attribute_3');

        foreach ($categories as $category) {
            $attrForFaceting[] = 'searchable('.$category->getGroupingAttribute1().')';
            $attrForFaceting[] = 'searchable('.$category->getGroupingAttribute2().')';
            $attrForFaceting[] = 'searchable('.$category->getGroupingAttribute3().')';
        }

        $indexSettings['attributesForFaceting'] = array_values(array_unique($attrForFaceting));

        $transport->setData($indexSettings);
    }
}