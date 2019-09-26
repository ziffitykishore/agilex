<?php

namespace Creatuity\Nav\Model\Data\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LogType implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'customer_approval', 'label' => __('Customer Approval')]
        ];
    }
}
