<?php

namespace SomethingDigital\Order\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use SomethingDigital\Sx\Logger\Logger;
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
use Magento\Directory\Model\Region;

class OrderPlaceApi extends Adapter
{
    protected $session;
    protected $addressRepository;
    protected $orderItemRepository;
    protected $customerRepository;
    protected $companyManagement;
    protected $companyRepository;
    protected $region;

    public function __construct(
        ClientFactory $curlFactory,
        Logger $logger,
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
        CompanyRepositoryInterface $companyRepository,
        Region $region
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
        $this->region = $region;
    }

    public function sendOrder($order)
    {
        if (!$this->isTestMode()) {
            $this->requestPath = 'api/Order';
        } else {
            $this->requestPath = 'api-mocks/Order/PlaceOrder';
        }

        $shipto = $this->getCustomerAddress($order, 'shipping');
        $shipToData = (!empty($shipto)) ? $this->assignAddressInformation($shipto) : '';
        $shipToData['ShipToPo'] = $order->getCheckoutShiptopo();

        $this->requestBody = [
            'SxId' => '',
            'ShipTo' => $shipToData,
            'Customer' => $this->getCustomerInfo($order),
            'LineItems' => $this->getItems($order),
            'externalIds' => $order->getIncrementId(),
            'PurchaseOrderId' => $order->getCheckoutPonumber(),
            'ShippingMethod' => $order->getShippingMethod(),
            'Date' => $order->getCreatedAt(),
            'Total' => $order->getGrandTotal(),
            'Tax' => $order->getTaxAmount(),
            'ShipFee' => $order->getShippingAmount(),
            "CouponCode" => $order->getCouponCode(),
            'DiscountAmount' => $order->getDiscountAmount(),
            'DeliveryNotes' => $order->getCheckoutDeliverypoint(),
            'Notes' => $order->getCheckoutOrdernotes(),
            'Payments' => $this->getPaymentInfo($order)
        ];

        return $this->postRequest();
    }

    /**
     * @return string
     */
    protected function getCustomerAccountId($order)
    {
        if (($accountId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_account_id'))) {
            return $accountId->getValue();
        } else {
            return $order->getTraversAccountId();
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

    protected function getCustomerInfo($order)
    {
        try {
            $customerFreightAccount = $order->getCustomerCustomerFreightAccount();
        } catch (\Exception $e) {
            $customerFreightAccount = '';
        }

        if ($order->getCustomerId()) {
            $company = $this->getCustomerCompany($order->getCustomerId());

            if ($company) {
                $companyAddress = [
                    "id" => $this->getCustomerContactId(),
                    "Company" => $company->getCompanyName(),
                    "ToName" => $company->getCompanyName(),
                    "Line1" => (isset($company->getStreet()[0])) ? $company->getStreet()[0] : '',
                    "City" => $company->getCity(),
                    "State" => $this->getRegionCodeById($company->getRegionId()),
                    "PostalCode" => $company->getPostcode(),
                    "CountryCode" => $company->getCountryId(),
                    "Phone" => $company->getTelephone()
                ];
            } else {
                $companyAddress= $this->assignAddressInformation($this->getCustomerAddress($order, 'billing'));
            }
        } else {
            $companyAddress= $this->assignAddressInformation($this->getCustomerAddress($order, 'billing'));
        }

        $customerInfo = [
            "Id" => $this->getCustomerAccountId($order),
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
                "CustomerId" => $this->getCustomerAccountId($order),
                "Addresses" => [],
                "MagentoId" => $order->getCustomerId(),
                "ProspectId" => ""
            ]
        ];

        if (!empty($this->getCustomerAddress($order, 'billing'))) {
            $addressArray = $this->getCustomerAddress($order, 'billing');

            $customerInfo["Contact"]["Addresses"][] = $this->assignAddressInformation($addressArray);
            $customerInfo["Contact"]["Fax"] = $addressArray['fax'];
        }

        return $customerInfo;
    }

    protected function getCustomerAddress($order, $addressType)
    {
        if ($addressType == 'billing') {
            $billingAddressObj = $order->getBillingAddress();
            if ($billingAddressObj) {
                return $billingAddressObj->getData();
            }
        }
        if ($addressType == 'shipping') {
            $shippingAddressObj = $order->getShippingAddress();
            if ($shippingAddressObj) {
                return $shippingAddressObj->getData();
            }
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
                    "DiscountAmount" => $discountAmount,
                    "DiscountSuffix" => $order->getSuffix()
                ];
            }
        }

        return $items;
    }

    public function assignAddressInformation($addressArray)
    {
        if ($addressArray['customer_address_id'] != null) {
            try {
                $addressObj = $this->addressRepository->getById($addressArray['customer_address_id']);
                $sxAddressId = $addressObj->getCustomAttribute('sx_address_id');
            } catch (NoSuchEntityException $e) {

            }
        }

        $address = explode(PHP_EOL, $addressArray['street']);

        return [
            "id" => (isset($sxAddressId) && $sxAddressId->getValue()) ? $sxAddressId->getValue() : '',
            "ToName" => $addressArray['firstname'] . ' ' . $addressArray['lastname'],
            "Company" => $addressArray['company'] ?? '',
            "Line1" => $address[0],
            "Line2" => $address[1] ?? '',
            "City" => $addressArray['city'],
            "State" => $this->getRegionCodeById($addressArray['region_id']),
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
        $customerCompany = false;
        try {
            $company = $this->companyManagement->getByCustomerId($customerId);
            if ($company) {
                $companyId = $company->getId();
                $customerCompany = $this->companyRepository->get($companyId);
            }
        } catch (NoSuchEntityException $noSuchEntityException) {
            //If company is not found - just return false
        }
        return $customerCompany;
    }

    public function getRegionCodeById($id)
    {
        try {
            $region = $this->region->load($id);
            return $region->getCode();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return '';
        }
    }
}
