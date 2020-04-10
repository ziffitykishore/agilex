<?php
declare(strict_types=1);

namespace Earthlite\Customer\Plugin\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\GroupFactory as CustomerGroupModelFacotry;
use Magento\Customer\Model\ResourceModel\GroupFactory as CustomerGroupResourceModelFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * class ABMPCustomersGroupChangePlugin
 *
 */
class ABMPCustomersGroupChangePlugin 
{
    const ABMP_GROUP_CODE = 'ABMP';
    
    const ABMP_MIN_LENGTH = 5;
    
    const ABMP_MAX_LENGTH = 8;
    
    const MIN_LENGTH_VALIDATION_MESSAGE = 'Please enter more or equal than 5 symbols.';

    const MAX_LENGTH_VALIDATION_MESSAGE = 'Please enter less or equal than 8 symbols.';
    
    const IS_NUMERIC_VALIDATION_MESSAGE = 'Please enter a valid number in this field.';
    
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
            $this->validateABMPNumber($ambpNumber->getValue());
            /** @var \Magento\Customer\Model\Group $customerGroupModel */
            $customerGroupModel = $this->customerGroupModelFactory->create();
            /** @var \Magento\Customer\Model\ResourceModel\Group $customerGroupResourceModel */
            $customerGroupResourceModel = $this->customerGroupResourceModelFactory->create();
            $customerGroupResourceModel->load($customerGroupModel, self::ABMP_GROUP_CODE, 'customer_group_code');
            $customer->setGroupId($customerGroupModel->getId());
        }
        return [$customer, $passwordHash];
    }
    
    /**
     * Validates length, digits
     * 
     * @param sring $abmpNumber
     * @throws LocalizedException
     */
    protected function validateABMPNumber($abmpNumber)
    {
        if (!is_numeric($abmpNumber)) {
            throw new LocalizedException(__(self::IS_NUMERIC_VALIDATION_MESSAGE));
        }
        $abmpStrLength = strlen($abmpNumber);
        if ($abmpStrLength < self::ABMP_MIN_LENGTH) {
            throw new LocalizedException(__(self::MIN_LENGTH_VALIDATION_MESSAGE));
        } elseif ($abmpStrLength > self::ABMP_MAX_LENGTH) {
            throw new LocalizedException(__(self::MAX_LENGTH_VALIDATION_MESSAGE));
        }

    }

}
