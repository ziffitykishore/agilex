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
            'searchableAttributes' => ['unordered(attribute)', 'unordered(items)']
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
            $attrOptionsArray = [];
            foreach ($attrOptions as $option) {
                if ($option->getValue()) {
                    $attrOptionsArray[] = $option->getLabel();
                }
            }
            if (!empty($attrOptionsArray)) {
                $attrOptionObject = [
                    'attribute' => $attr->getAttributeCode(),
                    'items' => $attrOptionsArray
                ];
                $attrOptionsData[] = $attrOptionObject;
            }
        }

        return $attrOptionsData;
    }
}
