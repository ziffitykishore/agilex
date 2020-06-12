<?php

namespace Earthlite\Customer\Plugin\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\GroupFactory as CustomerGroupModelFacotry;
use Magento\Customer\Model\ResourceModel\GroupFactory as CustomerGroupResourceModelFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * This class will change the customer group to AMTA
 * for the specified customer.
 */
class AMTACustomersGroupChangePlugin 
{
    /**
     * AMTA customer group code
     */
    const AMTA_GROUP_CODE = 'AMTA';

    /**
     * Min length for AMTA number
     */
    const AMTA_MIN_LENGTH = 4;

    /**
     * Max length for AMTA number
     */
    const AMTA_MAX_LENGTH = 9;

    /**
     * @var CustomerGroupModelFacotry
     */
    protected $customerGroupModelFactory;

    /**
     * @var CustomerGroupResourceModelFactory
     */
    protected $customerGroupResourceModelFactory;

    /**
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
     * Update Customer Group for AMTA Customers
     *
     * @param CustomerRepository $customerRepository
     * @param CustomerInterface $customer
     * @param string $passwordHash
     * @return array
     */
    public function beforeSave(
        CustomerRepository $customerRepository,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $amtaNumber = $customer->getCustomAttribute('amta_number');
        if ($amtaNumber && $amtaNumber->getValue()) {
            $this->validateAMTANumber($amtaNumber->getValue());
            $customerGroupModel = $this->customerGroupModelFactory->create();
            $customerGroupResourceModel = $this->customerGroupResourceModelFactory->create();
            $customerGroupResourceModel->load($customerGroupModel, self::AMTA_GROUP_CODE, 'customer_group_code');
            $customer->setGroupId($customerGroupModel->getId());
        }
        return [$customer, $passwordHash];
    }

    /**
     * Validates length, digits
     * 
     * @param string $amtaNumber
     * @throws LocalizedException
     * @return bool
     */
    protected function validateAMTANumber($amtaNumber)
    {
        if (!is_numeric($amtaNumber)) {
            throw new LocalizedException(__('Please enter a valid number in this field.'));
        }
        $amtaStrLength = strlen($amtaNumber);
        if ($amtaStrLength < self::AMTA_MIN_LENGTH) {
            throw new LocalizedException(__('Please enter more or equal than 4 symbols.'));
        } elseif ($amtaStrLength > self::AMTA_MAX_LENGTH) {
            throw new LocalizedException(__('Please enter less or equal than 9 symbols.'));
        }

        return true;
    }
}
