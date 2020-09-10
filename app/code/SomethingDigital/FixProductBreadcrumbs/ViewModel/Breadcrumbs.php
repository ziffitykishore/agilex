<?php

namespace SomethingDigital\FixProductBreadcrumbs\ViewModel;

use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;

class Breadcrumbs implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
    * @var Collection
    */
    private $collection;

    /**
    * @var Registry
    */
    private $coreRegistry;

    public function __construct(
        Collection $collection,
        Registry $registry
    ) {
        $this->collection = $collection;
        $this->coreRegistry = $registry;
    }
    
    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    public function getBreadcrumbs()
    {
        try {
            $product = $this->getProduct();
        } catch (LocalizedException $e) {
            return [];
        }


        $categoryIds = $product->getCategoryIds();

        try {
            $categoriesCollection = $this->collection
                ->addFieldToFilter('entity_id', array('in' => $categoryIds))
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('include_in_menu')
                ->addAttributeToSelect('is_active')
                ->addAttributeToSelect('category_ids')
                ->addAttributeToSelect('is_anchor');
        } catch (LocalizedException $e) {
            return [];
        }

        $categoryData = [];
        $categoriesLevel = [];

        foreach ($categoriesCollection as $category) {
            $categoryData[$category->getId()] = [
                'level' => $category->getLevel(),
                'name' => $category->getName(),
                'url' => $category->getUrl(),
                'path' => $category->getPathIds()
            ];
            $categoriesLevel[$category->getId()] = $category->getLevel();
        }

        arsort($categoriesLevel);

        $breadcrumbs = [];
        $prevLevel = 0;
        foreach ($categoriesLevel as $catId => $level) {
            if ($level < $prevLevel) {
                continue;
            }
            if (isset($categoryData[$catId])) {
                $breadcrumbData = [];
                foreach ($categoryData[$catId]['path'] as $value) {
                    if (isset($categoryData[$value])) {
                        $breadcrumbData [] = [
                            'name' => $categoryData[$value]['name'],
                            'url' => $categoryData[$value]['url']
                        ];
                    }
                }
                $breadcrumbData [] = [
                    'name' => '#'. $product->getSku(),
                    'url' => ''
                ];
                $breadcrumbs[] = $breadcrumbData;
            }
            $prevLevel = $level;
        }

        return $breadcrumbs;
    }

}
