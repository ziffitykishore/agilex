<?php

namespace SomethingDigital\Order\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use ShipperHQ\Shipper\Model\Order\Detail as ShipperHQDetail;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    protected $extensionFactory;
    protected $shipperHQDetail;
    protected $customerRepository;
    protected $addressRepository;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        OrderExtensionFactory $extensionFactory,
        ShipperHQDetail $shipperHQDetail,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->extensionFactory = $extensionFactory;
        $this->shipperHQDetail  = $shipperHQDetail;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }
    
    /**
     * Add extension attributes to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {

        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes = $this->setOrderExtensionAttribute($extensionAttributes, $order);
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    /**
     * Add extension attributes to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes = $this->setOrderExtensionAttribute($extensionAttributes, $order);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }

    /**
     * Set extension attributes
     *
     * @param $extensionAttributes
     * @param OrderInterface $order
     *
     * @return $extensionAttributes
     */
    public function setOrderExtensionAttribute($extensionAttributes, $order)
    {
        $shipperHQdetails = $this->shipperHQDetail->loadByOrder($order->getId())->getFirstItem();
        $extensionAttributes->setCustomerCarrier($shipperHQdetails->getCustomerCarrier() ?? "");
        $extensionAttributes->setCustomerCarrierPhoneNumber($shipperHQdetails->getCustomerCarrierPh() ?? "");
        $extensionAttributes->setFreightAccountNumber($shipperHQdetails->getCustomerCarrierAccount() ?? "");
        $extensionAttributes->setPurchaseOrderId($order->getCheckoutPonumber() ?? "");
        $extensionAttributes->setShipToPo($order->getCheckoutShiptopo() ?? "");
        $extensionAttributes->setDeliveryNotes($order->getCheckoutDeliverypoint() ?? "");
        $extensionAttributes->setNotes($order->getCheckoutOrdernotes() ?? "");
        $extensionAttributes->setCouponCode($order->getCouponCode() ?? "");
        $extensionAttributes->setCustomerId("");
        $extensionAttributes->setSxAddressId("");
        $extensionAttributes->setContactId("");

        if ($order->getCustomerId()) {
            try {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                $traversAccountId = $customer->getCustomAttribute('travers_account_id');
                if ($traversAccountId) {
                    $extensionAttributes->setCustomerId($traversAccountId->getValue());
                }
                $traversContactId = $customer->getCustomAttribute('travers_contact_id');
                if ($traversContactId) {
                    $extensionAttributes->setContactId($traversContactId->getValue());
                }
            } catch (NoSuchEntityException $e) {
                // action not needed
            }
        }
        $shippingAddress = $order->getShippingAddress();
        $sxAddressId = null;
        if ($shippingAddress) {
            $shippingAddress = $shippingAddress->getData();
            try {
                $addressObj = $this->addressRepository->getById($shippingAddress['customer_address_id']);
                $sxAddressId = $addressObj->getCustomAttribute('sx_address_id');
            } catch (NoSuchEntityException $e) {
                // action not needed
            }
        }
        if ($sxAddressId) {
            $extensionAttributes->setSxAddressId($sxAddressId->getValue());
        }

        return $extensionAttributes;
    }
}
