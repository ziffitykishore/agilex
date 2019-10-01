<?php

namespace PartySupplies\Sales\Model\Config\Source\Email;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $value = 'value';
        $label = 'label';
        
        return [
            [$value => 'cc', $label => __('Cc')],
            [$value => 'bcc', $label => __('Bcc')],
            [$value => 'copy', $label => __('Separate Email')],
        ];
    }
}
