<?php

namespace Ziffity\Pickupdate\Model\Config\Source;

class Style implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'as_is',
                'label' => __('As is')
            ),
            array(
                'value' => 'notice',
                'label' => __('Magento Notice')
            ),
        );
    }
}
