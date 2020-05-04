<?php

namespace Earthlite\LayerNavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer as LayerCatalog;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\CatalogSearch\Model\Layer\Filter\Category as AbstractFilter;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;
use Earthlite\LayerNavigation\Helper\Data as LayerHelper;

/**
 * @package Earthlite\LayerNavigation\Model\Layer\Filter
 */
class Category extends AbstractFilter
{   
    protected $_moduleHelper;

    protected $_isFilter = false;

    private $escaper;

    private $dataProvider;

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        LayerCatalog $layer,
        DataBuilder $itemDataBuilder,
        Escaper $escaper,
        CategoryFactory $dataProviderFactory,
        LayerHelper $moduleHelper,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $escaper, $dataProviderFactory, $data );

        $this->escaper       = $escaper;
        $this->_moduleHelper = $moduleHelper;
        $this->dataProvider  = $dataProviderFactory->create(['layer' => $this->getLayer()]);
    }


    public function apply(RequestInterface $request)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::apply($request);
        }

        $categoryId = $request->getParam($this->_requestVar);
        if (empty($categoryId)) {
            return $this;
        }

        $categoryIds = [];
        foreach (explode(',', $categoryId) as $id) {
            $this->dataProvider->setCategoryId($id);
            if ($this->dataProvider->isValid()) {
                $category = $this->dataProvider->getCategory();
                if ($request->getParam('id') !== $id) {
                    $categoryIds[] = $id;
                    $this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $id));
                }
            }
        }

        if (!empty($categoryIds)) {
            $this->_isFilter = true;
            $this->getLayer()->getProductCollection()->addLayerCategoryFilter($categoryIds);
        }

        if ($parentCategoryId = $request->getParam('id')) {
            $this->dataProvider->setCategoryId($parentCategoryId);
        }

        return $this;
    }

    protected function _getItemsData()
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        $productCollection = $this->getLayer()->getProductCollection();

        if ($this->_isFilter) {
            $productCollection = $productCollection->getCollectionClone()
                ->removeAttributeSearch('category_ids');
        }

        $optionsFacetedData = $productCollection->getFacetedData('category');
        $category           = $this->dataProvider->getCategory();
        $categories         = $category->getChildrenCategories();

        $collectionSize = $productCollection->getSize();

        if ($category->getIsActive()) {
            foreach ($categories as $childCategory) {
                $count = isset($optionsFacetedData[$childCategory->getId()])
                    ? $optionsFacetedData[$childCategory->getId()]['count'] : 0;
                if ($childCategory->getIsActive()
                    && $this->_moduleHelper->getFilterModel()->isOptionReducesResults($this, $count, $collectionSize)
                ) {
                    $this->itemDataBuilder->addItemData(
                        $this->escaper->escapeHtml($childCategory->getName()),
                        $childCategory->getId(),
                        $count
                    );
                }
            }
        }

        return $this->itemDataBuilder->build();
    }
}
