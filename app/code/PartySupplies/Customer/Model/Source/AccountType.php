<?php

namespace PartySupplies\Customer\Model\Source;

class AccountType extends \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend
{
        /**
         * Retrieve options array.
         *
         * @return array
         */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        return ['customer' => __('customer'),'company' => __('company')];
    }

        /**
         * @return array
         */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
