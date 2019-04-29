<?php

namespace SomethingDigital\CustomerSpecificPricing\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\ProductFactory;

class Data
{
   /**
    * @var SearchCriteriaBuilder
    */
    private $searchCriteriaBuilder;

   /**
    * @var ProductRepositoryInterface
    */
    private $productRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepo;
    
    /**
     * @var FilterGroupBuilder
     */
    private $groupBuilder;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        FilterBuilder $filterBuilder,
        AttributeRepositoryInterface $attributeRepo,
        FilterGroupBuilder $groupBuilder,
        ProductFactory $productFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->filterBuilder = $filterBuilder;
        $this->attributeRepo = $attributeRepo;
        $this->groupBuilder = $groupBuilder;
        $this->productFactory = $productFactory;
    }

    /**
     * Get assoicated products for grouped products
     *
     * @param ProductInterface
     * @return ProductInterface[]
     */
    public function getGroupedAssociatedProducts(ProductInterface $productData)
    {
        /** @var Grouped $typeInstance */
        $typeInstance = $productData->getTypeInstance();
        /** @var mixed[][] $wrapperArray */
        $wrapperArray = $typeInstance->getChildrenIds($productData->getId());
        /** @var int[] $childIds */
        $childIds = array_pop($wrapperArray);
        
        /** @var \Magento\Framework\Api\Filter[] $filter */
        $filters = [];
        foreach ($childIds as $id) {
            /** @var \Magento\Framework\Api\Filter $filter */
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('eq')
                ->setValue($id)
                ->create();
            
                $filters[] = $filter;
        }
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        $group = $this->groupBuilder
            ->setFilters($filters)
            ->create();
        $this->searchCriteriaBuilder->setFilterGroups([$group]);
        /** @var \Magento\Framework\Api\SearchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();
        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $children */
        $children = $this->productRepository->getList($searchCriteria)->getItems();
        return $children;
    }
    
    /**
     * Returns the option value for select and multiselect
     * custom attribute types for products
     * 
     * @param int $optionId
     * @param string $attributeCode
     * @return string|null
     */
    public function getOptionValue(int $optionId, string $attributeCode)
    {
        try {
            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $this->attributeRepo->get('catalog_product', $attributeCode);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        if ($attribute->usesSource()) {
            return  $attribute->getSource()->getOptionText($optionId);
        }
        return null;
    }

    /**
     * Get bundle product’s items
     *
     * @param ProductInterface
     * @return ProductInterface[]
     */
    public function getBundleProductOptionsData(ProductInterface $productData)
    {
        $product = $this->productFactory->create()->load($productData->getId());
        //get all the selection products used in bundle product.
        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
        return $selectionCollection;
    }
}

