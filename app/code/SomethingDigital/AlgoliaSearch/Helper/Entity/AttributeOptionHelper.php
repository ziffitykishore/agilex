<?php

namespace SomethingDigital\AlgoliaSearch\Helper\Entity;

use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class AttributeOptionHelper
{
    private $eventManager;
    private $configHelper;
    private $storeUrls;
    private $productAttributeRepository;
    private $productAttributeCollectionFactory;

    public function __construct(
        ManagerInterface $eventManager,
        ConfigHelper $configHelper,
        Repository $productAttributeRepository,
        CollectionFactory $productAttributeCollectionFactory
    ) {
        $this->eventManager = $eventManager;
        $this->configHelper = $configHelper;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }

    public function getIndexNameSuffix()
    {
        return '_attribute_option';
    }

    public function getIndexSettings()
    {
        $indexSettings = [
            'searchableAttributes' => ['unordered(attribute_id)', 'unordered(option_id)', 'unordered(option_label)']
        ];

        return $indexSettings;
    }

    public function getAttributesOptions()
    {
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $collection = $productAttributes->addFieldToFilter(
            ['is_filterable', 'is_searchable'],
            [true, true]
        );
        $attrOptionsData = [];
        foreach ($collection as $key => $attr) {
            $attrOptions = $attr->getOptions();
            $sort = 0;
            foreach ($attrOptions as $option) {
                if ($option->getValue()) {
                    $attrOptionObject = [
                        'attribute_id' => $attr->getId(),
                        'option_id' => $option->getValue(),
                        'option_label' => $option->getLabel(),
                        'sort_order' => $sort++
                    ];
                    $attrOptionsData[] = $attrOptionObject;
                }
            }
        }

        return $attrOptionsData;
    }
}
