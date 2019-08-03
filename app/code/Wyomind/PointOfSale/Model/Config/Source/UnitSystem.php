<?php

namespace Wyomind\PointOfSale\Model\Config\Source;

class UnitSystem implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['label' => 'Metric', 'value' => 0],
            ['label' => 'Imperial', 'value' => 1]
        ];
    }
}

