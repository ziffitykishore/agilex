<?php

/**
 * Get customer groups
 *
 * PHP version 7.1
 */

namespace Creatuity\Nav\Model\System\Config;

class Customergroups
{

    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;

    /**
     * Retrieve the customer groups
     *
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
    ) {
        $this->_customerGroup = $customerGroup;
    }

    /**
     * Get customer groups
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        $customerGroups = $this->_customerGroup->toOptionArray();
        return $customerGroups;
    }

    public function toOptionArray()
    {
        return $this->getCustomerGroups();
    }

}
