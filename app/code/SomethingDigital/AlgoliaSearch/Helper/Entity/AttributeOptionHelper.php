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
            'searchableAttributes' => [
                'unordered(attribute_code)',
                'unordered(option_label)',
                'unordered(sort_order)'
            ]
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
            $i = 0;
            foreach ($attrOptions as $option) {
                if ($option->getValue()) {
                    $attrOptionsData[] = [
                        'attribute_code' => $attr->getAttributeCode(),
                        'option_label' => $option->getLabel(),
                        'sort_order' => $i++
                    ];
                }
            }
        }

        return $attrOptionsData;
    }
}
