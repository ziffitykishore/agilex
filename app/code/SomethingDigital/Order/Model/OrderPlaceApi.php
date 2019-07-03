<?php

namespace SomethingDigital\Order\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Sales\Model\Order\ItemRepository;

class OrderPlaceApi extends Adapter
{
    protected $session;
    protected $addressRepository;

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode,
        AddressRepositoryInterface $addressRepository,
        ItemRepository $orderItemRepository
    ) {
        parent::__construct(
            $curlFactory,
            $logger,
            $config,
            $storeManager,
            $testMode
        );
        $this->session = $session;
        $this->addressRepository = $addressRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function sendOrder($order)
    {
        if (!$this->isTestMode()) {
            $this->requestPath = 'api/Order?customerId='.$this->getCustomerAccountId();
        } else {
            $this->requestPath = 'api-mocks/Order/PlaceOrder';
        }

        $this->requestBody = [
            'PurchaseOrderId' => '',
            'ShipServiceCode' => $order->getShippingMethod(),
            'Date' => $order->getCreatedAt(),
            'Total' => $order->getGrandTotal(),
            'Tax' => $order->getTaxAmount(),
            'ShipFee' => $order->getShippingAmount(),
            'DiscountAmount' => $order->getDiscountAmount(),
            'Notes' => '',
            'SxId' => '',
            'ShipTo' => $this->getShipto($order),
            'Customer' => $this->getCustomerInfo($order),
            'LineItems' => $this->getItems($order),
            'externalIds' => ''
        ];

        return $this->postRequest();
    }

    /**
     * @return string
     */
    protected function getCustomerAccountId()
    {
        if (($accountId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_account_id'))) {
            return $accountId->getValue();
        } else {
            return '';
        }
    }

    protected function getShipto($order)
    {
        $shippingAddressObj = $order->getShippingAddress();
        $shippingAddressArray = $shippingAddressObj->getData();

        $address = $this->addressRepository->getById($shippingAddressArray['customer_address_id']);
        $sxAddressId = $address->getCustomAttribute('sx_address_id');

        $shipto = [
            'id' => ($sxAddressId && $sxAddressId->getValue()) ? $sxAddressId->getValue() : '',
            'ToName' => $shippingAddressArray['firstname'] . ' ' . $shippingAddressArray['lastname'],
            'Line1' => $shippingAddressArray['street'],
            'City' => $shippingAddressArray['city'],
            'State' => $shippingAddressArray['region'],
            'PostalCode' => $shippingAddressArray['postcode'],
            'CountryCode' => $shippingAddressArray['country_id'],
            'Phone' => $shippingAddressArray['telephone']
        ];

        return $shipto;
    }

    protected function getCustomerInfo($order)
    {
        $billingAddressObj = $order->getBillingAddress();
        $billingAddressArray = $billingAddressObj->getData();

        $address = $this->addressRepository->getById($billingAddressArray['customer_address_id']);
        $sxAddressId = $address->getCustomAttribute('sx_address_id');

        $customerInfo = [
            "Id" => $this->getCustomerAccountId(),
            "Name" => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            "Address" => [
              "id" => ($sxAddressId && $sxAddressId->getValue()) ? $sxAddressId->getValue() : '',
              "ToName" => $billingAddressArray['firstname'] . ' ' . $billingAddressArray['lastname'],
              "Line1" => $billingAddressArray['street'],
              "City" => $billingAddressArray['city'],
              "State" => $billingAddressArray['region'],
              "PostalCode" => $billingAddressArray['postcode'],
              "CountryCode" => $billingAddressArray['country_id'],
              "Phone" => $billingAddressArray['telephone']
            ],
            "Comment" => '',
            "CustomerProductsFlag" => '',
            "Fax" => $billingAddressArray['fax'],
            "NotesIndicator" => '',
            "Phone" => $billingAddressArray['telephone'],
            "StatusType" => ''
        ];

        return $customerInfo;
    }

    protected function getItems($order)
    {
        $items = [];
        foreach ($order->getAllItems() as $key => $value) {
            if ($value->getProductType() == 'simple' || $value->getProductType() == 'virtual') {
                if ($value->getParentItemId()) {
                    $parent = $this->orderItemRepository->get($value->getParentItemId());
                    $price = $parent->getPrice();
                    $discountAmount = $parent->getDiscountAmount();
                } else {
                    $price = $value->getPrice();
                    $discountAmount = $value->getDiscountAmount();
                }
                $items []= [
                    "SKU" => $value->getSku(),
                    "Title" => $value->getName(),
                    "Qty" => $value->getQtyOrdered(),
                    "SoldPrice" => $price,
                    "CostAtTimeOfPurchase" => $price,
                    "DiscountAmount" => $discountAmount
                ];
            }
        }

        return $items;
    }

}
