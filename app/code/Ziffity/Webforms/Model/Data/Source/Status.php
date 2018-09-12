<?php

namespace Ziffity\Webforms\Model\Data\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'catalog', 'label' => __('Catalog')],
            ['value' => 'coin', 'label' => __('Coin')],
            ['value' => 'contact', 'label' => __('Contact')]
        ];
    }
}

