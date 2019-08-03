<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Model\System\Config\Source;

class FieldDelimiter
{

    protected $_dataHelper = null;
    
    public function __construct(
        \Wyomind\MassStockUpdate\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    public function toOptionArray()
    {
        $data = [];
        foreach ($this->_dataHelper->getFieldDelimiters() as $key => $value) {
            $data[] = ['value' => $key, 'label' => $value];
        }

        return $data;
    }
}
