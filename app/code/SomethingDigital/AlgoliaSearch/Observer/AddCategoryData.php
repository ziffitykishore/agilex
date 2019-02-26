<?php

namespace SomethingDigital\AlgoliaSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class AddCategoryData implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $category = $observer->getEvent()->getData('category');
        $transport = $observer->getEvent()->getData('categoryObject');
        $this->addParentCategoryId($category, $transport);
        
    }

    /**
     * Add parent_category_id attribute to algolia data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Framework\DataObject $transport
     */
    private function addParentCategoryId($category, $transport)
    {
        $algoliaCategoryData = $transport->getData();
        $parentCategoryId = $category->getParentId();
        $algoliaCategoryData['parent_category_id'] = $parentCategoryId;
        $transport->setData($algoliaCategoryData);
    }

    

    
}
