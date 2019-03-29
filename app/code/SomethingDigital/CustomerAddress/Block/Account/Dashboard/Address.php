<?php

namespace SomethingDigital\CustomerAddress\Block\Account\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;

class Address extends \Magento\Customer\Block\Account\Dashboard\Address
{
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
     */
    protected $currentCustomerAddress;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        array $data = []
    ) {
        parent::__construct($context, $currentCustomer, $currentCustomerAddress, $addressConfig, $addressMapper, $data);
        $this->addressRepository = $addressRepository;
    }
    
    /**
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    public function getAddressById($addressId)
    {
        try {
            return $this->addressRepository->getById($addressId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}