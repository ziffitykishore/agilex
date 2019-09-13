<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Framework\DataObject;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;

class AttributesDataProcessor implements DataProcessorInterface
{
    protected $attributes = [];
    protected $productResourceModel;

    public function __construct(
        array $attributes = [],
        ProductResourceModel $productResourceModel
    ) {
        $this->attributes = $attributes;
        $this->productResourceModel = $productResourceModel;
    }

    public function process(DataObject $productData, DataObject $intermediateData)
    {
        foreach ($this->attributes as $attribute) {
            $productData->setData($attribute, $intermediateData->getData($attribute));
            $this->productResourceModel->saveAttribute($productData, $attribute);
        }
    }
}
