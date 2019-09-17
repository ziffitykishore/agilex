<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Directory\Model\RegionFactory;

class CustomerFieldOrderDataExtractor implements OrderDataExtractorInterface
{
    protected $accessorMethod;
    protected $customerRepository;
    protected $addressRepository;
    protected $regionFactory;

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

    public function extract(OrderInterface $order)
    {
        return $this->{$this->accessorMethod}($order);
    }

    protected function getCustomerName(OrderInterface $order)
    {
        if ($order->getCustomerIsGuest()) {
            return $order->getBillingAddress()->getName();
        }

        return $order->getCustomerName();
    }

    protected function getCustomerEmail(OrderInterface $order)
    {
        return $order->getCustomerEmail();
    }

    protected function getCustomerStreetFirst(OrderInterface $order)
    {
        return $this->getCustomerStreetLine($order, 0);
    }

    protected function getCustomerStreetSecond(OrderInterface $order)
    {
        return $this->getCustomerStreetLine($order, 1);
    }

    protected function getCustomerCity(OrderInterface $order)
    {
        return $this->getCustomerBillingAddress($order)->getCity();
    }

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

    protected function getCustomerPostcode(OrderInterface $order)
    {
        return $this->getCustomerBillingAddress($order)->getPostcode();
    }

    protected function getCustomerStreetLine(OrderInterface $order, $streetIndex)
    {
        $street = $this->getCustomerBillingAddress($order)->getStreet();

        return $street[$streetIndex] ?? '';
    }

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

    protected function getCustomer(OrderInterface $order)
    {
        return $this->customerRepository->getById($order->getCustomerId());
    }
}
