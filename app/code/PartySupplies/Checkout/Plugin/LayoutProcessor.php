<?php
namespace PartySupplies\Checkout\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\AddressFactory;


class LayoutProcessor
{
    public $customerSession;
    public $customers;
    public $address;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customers,
        AddressFactory $address
    ) {
        $this->customerSession = $customerSession;
        $this->customers = $customers;
        $this->address = $address;
    }

    /**
    * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
    * @param array $jsLayout
    * @return array
    */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $customerId = $this->customerSession->getCustomerId();
        $value = $this->customers->getById($customerId);
        $addressId = $value->getDefaultShipping();
        $shippingAddress = $this->address->create()->load($addressId);
        $company = $shippingAddress->getCompany();

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['value'] = $company;

        $payment_list = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
        foreach($payment_list as &$payment) {
            if(
                array_key_exists('children',$payment) && 
                array_key_exists('form-fields',$payment['children']) && 
                array_key_exists('children',$payment['children']['form-fields'])) {
                $payment['children']['form-fields']['children']['company']['value'] = $company;
                $payment['children']['form-fields']['children']['telephone']['validation']['validate-phoneStrict'] = true;
            }
        }
        return $jsLayout;
    }
}