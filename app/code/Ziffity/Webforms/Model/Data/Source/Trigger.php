<?php

namespace Ziffity\Webforms\Model\Data\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Trigger implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Enabled')],
            ['value' => 1, 'label' => __('Disabled')]
        ];
    }
}

