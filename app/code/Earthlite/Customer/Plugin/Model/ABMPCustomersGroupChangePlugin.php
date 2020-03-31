<?php
declare(strict_types=1);

namespace Earthlite\Customer\Plugin\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\GroupFactory as CustomerGroupModelFacotry;
use Magento\Customer\Model\ResourceModel\GroupFactory as CustomerGroupResourceModelFactory;

/**
 * class ABMPCustomersGroupChangePlugin
 *
 */
class ABMPCustomersGroupChangePlugin 
{
    const ABMP_GROUP_CODE = 'ABMP';
    
    /**
     * @var CustomerGroupModelFacotry
     */
    protected $customerGroupModelFactory;
    
    /**
     * @var CustomerGroupResourceModelFactory
     */
    protected $customerGroupResourceModelFactory;


    /**
     * ABMPCustomersGroupChangePlugin constructor 
     * @param CustomerGroupModelFacotry $customerGroupModelFactory
     * @param CustomerGroupResourceModelFactory $customerGroupResourceModelFactory
     */
    public function __construct(
        CustomerGroupModelFacotry $customerGroupModelFactory,
        CustomerGroupResourceModelFactory $customerGroupResourceModelFactory
    ) {
        $this->customerGroupModelFactory = $customerGroupModelFactory;
        $this->customerGroupResourceModelFactory = $customerGroupResourceModelFactory;
    }

    /**
     * Update Customer Group for ABMP Customers
     * @param CustomerInterface $customer
     * @param type $passwordHash
     * @return array
     */
    public function beforeSave(
        CustomerRepository $customerRepository, 
        CustomerInterface $customer, 
        $passwordHash = null
    ) {
        $ambpNumber = $customer->getCustomAttribute('abmp_number');
        if ($ambpNumber && $ambpNumber->getValue()) {
            /** @var \Magento\Customer\Model\Group $customerGroupModel */
            $customerGroupModel = $this->customerGroupModelFactory->create();
            /** @var \Magento\Customer\Model\ResourceModel\Group $customerGroupResourceModel */
            $customerGroupResourceModel = $this->customerGroupResourceModelFactory->create();
            $customerGroupResourceModel->load($customerGroupModel, self::ABMP_GROUP_CODE, 'customer_group_code');
            $customer->setGroupId($customerGroupModel->getId());
        }
        return [$customer, $passwordHash];
    }

}
