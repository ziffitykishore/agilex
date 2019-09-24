<?php

namespace Creatuity\Nav\Model\Data\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Success')],
            ['value' => 0, 'label' => __('Failure')]
        ];
    }
}
