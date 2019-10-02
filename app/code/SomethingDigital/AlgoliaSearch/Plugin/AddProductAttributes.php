<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class AddProductAttributes
{
    private $customerGroupRepository;
    private $searchCriteriaBuilder;

    public function __construct(
        GroupRepositoryInterface $customerGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerGroupRepository = $customerGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Add configuration for additional product attributes to Algolia index
     *
     * @param \Algolia\AlgoliaSearch\Helper\ConfigHelper $subject
     * @param type $result
     * @param type $storeId
     */
    public function afterGetProductAdditionalAttributes(\Algolia\AlgoliaSearch\Helper\ConfigHelper $subject, $result, $storeId = null)
    {
        $result[] = [
            'attribute' => 'manufacturer_price',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $result[] = [
            'attribute' => 'min_sale_qty',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $result[] = [
            'attribute' => 'qty_increment',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $result[] = [
            'attribute' => 'exact_unit_price',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $result[] = [
            'attribute' => 'manufacturer_exact_unit_price',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $result[] = [
            'attribute' => 'special_exact_unit_price',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        $customerGroups = $this->customerGroupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($customerGroups as $customerGroup) {
            $result[] = [
                'attribute' => 'group_' . $customerGroup->getId() . '_tiers',
                'searchable' => 2,
                'order' => 'unordered',
                'retrievable' => 1
            ];
        }
        return $result;
    }
}
