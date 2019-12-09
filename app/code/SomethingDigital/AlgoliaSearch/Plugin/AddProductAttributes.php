<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;

class AddProductAttributes
{
    private $customerGroupRepository;
    private $searchCriteriaBuilder;
    private $resourceConnection;
    private $additionalAttributes;

    public function __construct(
        GroupRepositoryInterface $customerGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ResourceConnection $resourceConnection
    ) {
        $this->customerGroupRepository = $customerGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
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
        if ($this->additionalAttributes !== null) {
            return $this->additionalAttributes;
        }
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

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(['ea' => $connection->getTableName('eav_attribute')], 'ea.attribute_code')
            ->join(['cea' => $connection->getTableName('catalog_eav_attribute')], 'ea.attribute_id = cea.attribute_id')
            ->where('(cea.is_searchable = 1 OR cea.is_filterable = 1)');

        $attrCollection = $connection->fetchAll($select);

        foreach ($attrCollection as $item) {
            $result[] = [
                "attribute" => $item['attribute_code'],
                "searchable"=> 1,
                "order"=> "unordered",
                "retrievable"=> 1
            ];
        }

        $this->additionalAttributes = $result;
        return $result;
    }
}
