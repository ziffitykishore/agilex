<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Directory\Model\RegionFactory;

class CustomerFieldOrderDataExtractor implements OrderDataExtractorInterface
{
    /**
     * @var string
     */
    protected $accessorMethod;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var regionFactory
     */
    protected $regionFactory;

    /**
     *
     * @param string $accessorMethod
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        $accessorMethod,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        RegionFactory $regionFactory
    ) {
        $this->accessorMethod = $accessorMethod;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * To extract information from Magento order data
     *
     * @param OrderInterface $order
     * @return mixed
     */
    public function extract(OrderInterface $order)
    {
        return $this->{$this->accessorMethod}($order);
    }

    /**
     * To get customer name
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerName(OrderInterface $order)
    {
        if ($order->getCustomerIsGuest()) {
            return $order->getBillingAddress()->getName();
        }

        return $order->getCustomerName();
    }

    /**
     * To get customer email
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerEmail(OrderInterface $order)
    {
        return $order->getCustomerEmail();
    }

    /**
     * To get NAV customer id
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getNavisionId(OrderInterface $order)
    {
        return $order->getNavCustomerId();
    }

    /**
     * To get increment id
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getIncrementId(OrderInterface $order)
    {
        return $order->getIncrementId();
    }

    /**
     * To get company name
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCompanyName(OrderInterface $order)
    {
        return $this->getCustomerBillingAddress($order)->getCompany();
    }

    /**
     * To get customer street line first from billing address
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerStreetFirst(OrderInterface $order)
    {
        return $this->getCustomerStreetLine($order, 0);
    }

    /**
     * To get customer street line second from billing address
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerStreetSecond(OrderInterface $order)
    {
        return $this->getCustomerStreetLine($order, 1);
    }

    /**
     * To get customer city from billing address
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerCity(OrderInterface $order)
    {
        return $this->getCustomerBillingAddress($order)->getCity();
    }

    /**
     * To get customer region code from billing address
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerRegionCode(OrderInterface $order)
    {
        $address = $this->getCustomerBillingAddress($order);

        if ($order->getCustomerIsGuest()) {
            return $address->getRegionCode();
        }

        $region = $address->getRegion();
        if (is_string($region)) {
            return $this->regionFactory->create()->loadByName(
                $address->getRegion(),
                $address->getCountryId()
            )->getCode();
        }

        return $region->getRegionCode();
    }

    /**
     * To get customer postcode from billing address
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getCustomerPostcode(OrderInterface $order)
    {
        return $this->getCustomerBillingAddress($order)->getPostcode();
    }

    /**
     * To get customer street line address
     *
     * @param OrderInterface $order
     * @param int $streetIndex
     * @return string
     */
    protected function getCustomerStreetLine(OrderInterface $order, $streetIndex)
    {
        $street = $this->getCustomerBillingAddress($order)->getStreet();

        return $street[$streetIndex] ?? '';
    }

    /**
     * To get customer default billing address
     *
     * @param OrderInterface $order
     * @return AddressRepositoryInterface
     */
    protected function getCustomerBillingAddress(OrderInterface $order)
    {
        if ($order->getCustomerIsGuest()) {
            return $order->getBillingAddress();
        }

        $defaultBillingAddressId = $this->getCustomer($order)->getDefaultBilling();
        if ($defaultBillingAddressId === null) {
            return $order->getBillingAddress();
        }

        return $this->addressRepository->getById($defaultBillingAddressId);
    }

    /**
     * To get customer
     *
     * @param OrderInterface $order
     * @return CustomerRepositoryInterface
     */
    protected function getCustomer(OrderInterface $order)
    {
        return $this->customerRepository->getById($order->getCustomerId());
    }
}
