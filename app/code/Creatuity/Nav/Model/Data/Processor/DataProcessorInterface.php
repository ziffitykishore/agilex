<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Framework\DataObject;

interface DataProcessorInterface
{
    public function process(DataObject $productData, DataObject $intermediateData);
}
