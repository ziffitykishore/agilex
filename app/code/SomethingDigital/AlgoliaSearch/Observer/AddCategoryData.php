<?php

namespace SomethingDigital\AlgoliaSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryRepository;


class AddCategoryData implements ObserverInterface
{
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository
    ) {
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $category = $observer->getEvent()->getData('category');
        $transport = $observer->getEvent()->getData('categoryObject');
        $this->addCategoryId($category, $transport);
        $this->addParentCategoryId($category, $transport);
        $this->addGrouping($category, $transport);
        $this->removePubDirectory($category, $transport);
        
    }

    /**
     * Add category_id attribute to algolia data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Framework\DataObject $transport
     */
    private function addCategoryId($category, $transport)
    {
        $algoliaCategoryData = $transport->getData();
        $categoryId = $category->getId();
        $algoliaCategoryData['category_id'] = $categoryId;
        $transport->setData($algoliaCategoryData);
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


    /**
     * Add grouping attributes to algolia data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Framework\DataObject $transport
     */
    private function addGrouping($category, $transport)
    {
        $algoliaCategoryData = $transport->getData();
        $algoliaCategoryData['grouping'] = [];

        $cat = $this->categoryRepository->get($category->getId(), $category->getStoreId());

        if (!empty($cat->getGroupingAttribute1())) {
            $algoliaCategoryData['grouping'][] = $cat->getGroupingAttribute1();
        }
        if (!empty($cat->getGroupingAttribute2())) {
            $algoliaCategoryData['grouping'][] = $cat->getGroupingAttribute2();
        }
        if (!empty($cat->getGroupingAttribute3())) {
            $algoliaCategoryData['grouping'][] = $cat->getGroupingAttribute3();
        }

        $transport->setData($algoliaCategoryData);
    }

    /**
     * Remove /pub directory
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Framework\DataObject $transport
     */
    private function removePubDirectory($category, $transport)
    {
        $algoliaCategoryData = $transport->getData();
        $algoliaCategoryData['image_url'] = str_replace('/pub/', '/', $category->getImageUrl());
        
        $transport->setData($algoliaCategoryData);
    }
    
}
