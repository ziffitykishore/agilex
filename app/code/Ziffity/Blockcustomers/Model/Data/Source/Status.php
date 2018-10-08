<?php

namespace Ziffity\Blockcustomers\Model\Data\Source;

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
            ['value' => 1, 'label' => __('Blocked')],
            ['value' => 0, 'label' => __('Not Blocked')]
        ];
    }
}
