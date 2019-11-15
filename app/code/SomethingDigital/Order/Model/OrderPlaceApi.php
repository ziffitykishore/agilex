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
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class OrderPlaceApi extends Adapter
{
    protected $session;
    protected $addressRepository;
    protected $orderItemRepository;
    protected $customerRepository;
    protected $companyManagement;
    protected $companyRepository;

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode,
        AddressRepositoryInterface $addressRepository,
        ItemRepository $orderItemRepository,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor,
        CustomerRepositoryInterface $customerRepository,
        CompanyManagementInterface $companyManagement,
        CompanyRepositoryInterface $companyRepository
    ) {
        parent::__construct(
            $curlFactory,
            $logger,
            $config,
            $storeManager,
            $testMode,
            $configWriter,
            $cacheTypeList,
            $encryptor
        );
        $this->session = $session;
        $this->addressRepository = $addressRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->customerRepository = $customerRepository;
        $this->companyManagement = $companyManagement;
        $this->companyRepository = $companyRepository;
    }

    public function sendOrder($order)
    {
        if (!$this->isTestMode()) {
            $this->requestPath = 'api/Order';
        } else {
            $this->requestPath = 'api-mocks/Order/PlaceOrder';
        }

        $this->requestBody = [
            'SxId' => '',
            'ShipTo' => $this->getShipto($order),
            'Customer' => $this->getCustomerInfo($order),
            'LineItems' => $this->getItems($order),
            'externalIds' => '',
            'PurchaseOrderId' => '',
            'ShipServiceCode' => $order->getShippingMethod(),
            'Date' => $order->getCreatedAt(),
            'Total' => $order->getGrandTotal(),
            'Tax' => $order->getTaxAmount(),
            'ShipFee' => $order->getShippingAmount(),
            'DiscountAmount' => $order->getDiscountAmount(),
            'CreditTermsMessage' => '',
            'Notes' => '',
            'Payments' => $this->getPaymentInfo($order)
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

    /**
     * @return string
     */
    protected function getCustomerContactId()
    {
        if (($contactId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_contact_id'))) {
            return $contactId->getValue();
        } else {
            return '';
        }
    }

    protected function getShipto($order)
    {
        $shipto = [];
        $shippingAddressObj = $order->getShippingAddress();

        if ($shippingAddressObj) {
            $shippingAddressArray = $shippingAddressObj->getData();
            $shipto = $this->assignAddressInformation($shippingAddressArray);
        }

        return $shipto;
    }

    protected function getCustomerInfo($order)
    {
        $customerFreightAccount = '';

        if ($order->getCustomerId()) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            if ($customer->getCustomAttribute('customer_freight_account')) {
                $customerFreightAccount = $customer->getCustomAttribute('customer_freight_account')->getValue();
            }

            $company = $this->getCustomerCompany($order->getCustomerId());

            if ($company) {
                $companyAddress = [
                    "id" => $this->getCustomerContactId(),
                    "ToName" => $company->getCompanyName(),
                    "Line1" => (isset($company->getStreet()[0])) ? $company->getStreet()[0] : '',
                    "City" => $company->getCity(),
                    "State" => $company->getRegion(),
                    "PostalCode" => $company->getPostcode(),
                    "CountryCode" => $company->getCountryId(),
                    "Phone" => $company->getTelephone()
                ];
            } else {
                $companyAddress= $this->assignAddressInformation($this->getCustomerBillingAddress($order));
            }
        } else {
            $companyAddress= $this->assignAddressInformation($this->getCustomerBillingAddress($order));
        }

        $customerInfo = [
            "Id" => $this->getCustomerAccountId(),
            "Name" => $companyAddress['ToName'],
            "Address" => $companyAddress,
            "Comment" => '',
            "CustomerProductsFlag" => '',
            "NotesIndicator" => '',
            "Phone" => $companyAddress['Phone'],
            "StatusType" => '',
            "FreightAccountNumber" => $customerFreightAccount,
            "Contact" => [
                "Id" => $this->getCustomerContactId(),
                "ContactType" => "",
                "TypeDescription" => "",
                "Email" => $order->getCustomerEmail(),
                "Fax" => "",
                "FirstName" => $order->getCustomerFirstname(),
                "MiddleName" => $order->getCustomerMiddlename(),
                "LastName" => $order->getCustomerLastname(),
                "GroupCode" => "",
                "CustomerId" => $this->getCustomerAccountId(),
                "Addresses" => [],
                "MagentoId" => $order->getIncrementId(),
                "ProspectId" => ""
            ]
        ];

        if (!empty($this->getCustomerBillingAddress($order))) {
            $addressArray = $this->getCustomerBillingAddress($order);

            $customerInfo["Contact"]["Addresses"][] = $this->assignAddressInformation($addressArray);
            $customerInfo["Contact"]["Fax"] = $addressArray['fax'];
        }

        return $customerInfo;
    }

    protected function getCustomerBillingAddress($order)
    {
        $billingAddressObj = $order->getBillingAddress();

        if ($billingAddressObj) {
            return $billingAddressObj->getData();
        }
        return [];
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
                    "Qty" => intval($value->getQtyOrdered()),
                    "SoldPrice" => $price,
                    "CostAtTimeOfPurchase" => $price,
                    "DiscountAmount" => $discountAmount
                ];
            }
        }

        return $items;
    }

    public function assignAddressInformation($addressArray)
    {
        if ($addressArray['customer_address_id'] != null) {
            $addressObj = $this->addressRepository->getById($addressArray['customer_address_id']);
            $sxAddressId = $addressObj->getCustomAttribute('sx_address_id');
        }

        return [
            "id" => (isset($sxAddressId) && $sxAddressId->getValue()) ? $sxAddressId->getValue() : '',
            "ToName" => $addressArray['firstname'] . ' ' . $addressArray['lastname'],
            "Line1" => $addressArray['street'],
            "City" => $addressArray['city'],
            "State" => $addressArray['region'],
            "PostalCode" => $addressArray['postcode'],
            "CountryCode" => $addressArray['country_id'],
            "Phone" => $addressArray['telephone']
        ];
    }

    public function getPaymentInfo($order)
    {
        $additionalInformation = $order->getPayment()->getAdditionalInformation();

        return [$additionalInformation];
    }

    public function getCustomerCompany($customerId)
    {
        try {
            $companyId = $this->companyManagement->getByCustomerId($customerId)->getId();
            return $this->companyRepository->get($companyId);
        } catch (NoSuchEntityException $noSuchEntityException) {
            return false;
        }
    }

}
