<?php

namespace Ziffity\Avalara\Plugin;

use ClassyLlama\AvaTax\Framework\Interaction\Tax as AvalaraTax;
use AvaTax\DetailLevel;
use AvaTax\DocumentType;
use AvaTax\GetTaxRequest;
use AvaTax\GetTaxRequestFactory;
use AvaTax\TaxOverrideFactory;
use AvaTax\TaxServiceSoap;
use AvaTax\TaxServiceSoapFactory;
use ClassyLlama\AvaTax\Framework\Interaction\MetaData\MetaDataObjectFactory;
use ClassyLlama\AvaTax\Helper\Config;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use ClassyLlama\AvaTax\Framework\Interaction\MetaData\ValidationException;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class Tax extends AvalaraTax
{
    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @var Config
     */
    protected $config = null;

    /**
     * @var \ClassyLlama\AvaTax\Helper\TaxClass
     */
    protected $taxClassHelper;

    /**
     * @var \ClassyLlama\AvaTax\Model\Logger\AvaTaxLogger
     */
    protected $avaTaxLogger;

    /**
     * @var MetaData\MetaDataObject
     */
    protected $metaDataObject = null;

    /**
     * @var TaxServiceSoapFactory
     */
    protected $taxServiceSoapFactory = [];

    /**
     * @var GetTaxRequestFactory
     */
    protected $getTaxRequestFactory = null;

    /**
     * @var TaxOverrideFactory
     */
    protected $taxOverrideFactory = null;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository = null;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository = null;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository = null;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository = null;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var Line
     */
    protected $interactionLine = null;

    /**
     * @var TaxServiceSoap[]
     */
    protected $taxServiceSoap = [];

    /**
     * @var TaxCalculation
     */
    protected $taxCalculation = null;

    /**
     * List of types that we want to be used with setType
     *
     * @var array
     */
    protected $simpleTypes = ['boolean', 'integer', 'string', 'double'];

    /**
     * A list of valid fields for the data array and meta data about their types to use in validation
     * based on the API documentation.  If any fields are added or removed, the same should be done in getGetTaxRequest.
     *
     * @var array
     */
    public static $validFields = [
        'StoreId' => ['type' => 'integer'],
        'BusinessIdentificationNo' => ['type' => 'string', 'length' => 25],
        'Commit' => ['type' => 'boolean'],
        // Company Code is not required by the the API, but we are requiring it in this integration
        'CompanyCode' => ['type' => 'string', 'length' => 25, 'required' => true],
        'CurrencyCode' => ['type' => 'string', 'length' => 3],
        'CustomerCode' => ['type' => 'string', 'length' => 50, 'required' => true],
        'CustomerUsageType' => ['type' => 'string', 'length' => 25],
        'DestinationAddress' => ['type' => 'object', 'class' => '\AvaTax\Address', 'required' => true],
        'DetailLevel' => [
            'type' => 'string',
            'options' => ['Document', 'Diagnostic', 'Line', 'Summary', 'Tax']
        ],
        'Discount' => ['type' => 'double'],
        'DocCode' => ['type' => 'string', 'length' => 50],
        'DocDate' => ['type' => 'string', 'format' => '/\d\d\d\d-\d\d-\d\d/', 'required' => true],
        'DocType' => [
            'type' => 'string',
            'options' =>
                ['SalesOrder', 'SalesInvoice', 'PurchaseOrder', 'PurchaseInvoice', 'ReturnOrder', 'ReturnInvoice'],
            'required' => true,
        ],
        'ExchangeRate' => ['type' => 'double'],
        'ExchangeRateEffDate' => ['type' => 'string', 'format' => '/\d\d\d\d-\d\d-\d\d/'],
        'ExemptionNo' => ['type' => 'string', 'length' => 25],
        'Lines' => [
            'type' => 'array',
            'length' => 15000,
            'subtype' => ['*' => ['type' => 'object', 'class' => '\AvaTax\Line']],
            'required' => true,
        ],
        'LocationCode' => ['type' => 'string', 'length' => 50],
        'OriginAddress' => ['type' => 'object', 'class' => '\AvaTax\Address'],
        'PaymentDate' => ['type' => 'string', 'format' => '/\d\d\d\d-\d\d-\d\d/'],
        'PurchaseOrderNo' => ['type' => 'string', 'length' => 50],
        'ReferenceCode' => ['type' => 'string', 'length' => 50],
        'SalespersonCode' => ['type' => 'string', 'length' => 25],
        'TaxOverride' => ['type' => 'object', 'class' => '\AvaTax\TaxOverride'],
        'IsSellerImporterOfRecord' => ['type' => 'boolean'],
    ];

    public static $validTaxOverrideFields = [
        'Reason' => ['type' => 'string', 'required' => true],
        'TaxOverrideType' => [
            'type' => 'string',
            'options' => ['None', 'TaxAmount', 'Exemption', 'TaxDate'],
            'required' => true,
        ],
        'TaxDate' => ['type' => 'string', 'format' => '/\d\d\d\d-\d\d-\d\d/'],
        'TaxAmount' => ['type' => 'double'],
    ];

    /**
     * Format for the AvaTax dates
     */
    const AVATAX_DATE_FORMAT = 'Y-m-d';

    /**
     * Prefix for the DocCode field
     */
    const AVATAX_DOC_CODE_PREFIX = 'quote-';

    /**
     * Reason for AvaTax override for creditmemos to specify tax date
     */
    const AVATAX_CREDITMEMO_OVERRIDE_REASON = 'Adjustment for return';

    /**
     * Reason for AvaTax override for invoice to specify tax date
     */
    const AVATAX_INVOICE_OVERRIDE_REASON = 'TaxDate reflects Order Date (not Magento invoice date)';

    /**
     * Magento and AvaTax calculate tax rate differently (8.25 and 0.0825, respectively), so this multiplier is used to
     * convert AvaTax rate to Magento's rate
     */
    const RATE_MULTIPLIER = 100;

    /**
     * Default currency exchange rate
     */
    const DEFAULT_EXCHANGE_RATE = 1;

    /**
     * Class constructor
     *
     * @param Address $address
     * @param Config $config
     * @param \ClassyLlama\AvaTax\Helper\TaxClass $taxClassHelper
     * @param \ClassyLlama\AvaTax\Model\Logger\AvaTaxLogger $avaTaxLogger
     * @param MetaDataObjectFactory $metaDataObjectFactory
     * @param TaxServiceSoapFactory $taxServiceSoapFactory
     * @param GetTaxRequestFactory $getTaxRequestFactory
     * @param TaxOverrideFactory $taxOverrideFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param TimezoneInterface $localeDate
     * @param Line $interactionLine
     * @param TaxCalculation $taxCalculation
     * @param QuoteDetailsItemExtensionFactory $extensionFactory
     */
    public function __construct(
        \ClassyLlama\AvaTax\Framework\Interaction\Address $address,
        Config $config,
        \ClassyLlama\AvaTax\Helper\TaxClass $taxClassHelper,
        \ClassyLlama\AvaTax\Model\Logger\AvaTaxLogger $avaTaxLogger,
        MetaDataObjectFactory $metaDataObjectFactory,
        TaxServiceSoapFactory $taxServiceSoapFactory,
        GetTaxRequestFactory $getTaxRequestFactory,
        TaxOverrideFactory $taxOverrideFactory,
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        StoreRepositoryInterface $storeRepository,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $localeDate,
        \ClassyLlama\AvaTax\Framework\Interaction\Line $interactionLine,
        \ClassyLlama\AvaTax\Framework\Interaction\TaxCalculation $taxCalculation,
        QuoteDetailsItemExtensionFactory $extensionFactory
    ) {
        parent::__construct(
            $address,
            $config,
            $taxClassHelper,
            $avaTaxLogger,
            $metaDataObjectFactory,
            $taxServiceSoapFactory,
            $getTaxRequestFactory,
            $taxOverrideFactory,
            $customerRepository,
            $groupRepository,
            $invoiceRepository,
            $orderRepository,
            $storeRepository,
            $priceCurrency,
            $localeDate,
            $interactionLine,
            $taxCalculation,
            $extensionFactory
        );
    }   
    
    /**
     * Convert Tax Quote Details into data to be converted to a GetTax Request
     *
     * @param \Magento\Tax\Api\Data\QuoteDetailsInterface $taxQuoteDetails
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array|null
     */
    protected function convertTaxQuoteDetailsToData(
        \Magento\Tax\Api\Data\QuoteDetailsInterface $taxQuoteDetails,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $lines = [];

        $items = $taxQuoteDetails->getItems();
        $keyedItems = $this->taxCalculation->getKeyedItems($items);
        $childrenItems = $this->taxCalculation->getChildrenItems($items);

        /** @var \Magento\Tax\Api\Data\QuoteDetailsItemInterface $item */
        foreach ($keyedItems as $item) {
            /**
             * If a quote has children and they are calculated (e.g., Bundled products with dynamic pricing)
             * @see \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector::mapItems
             * then we only need to pass child items to AvaTax. Due to the logic in
             * @see \ClassyLlama\AvaTax\Framework\Interaction\TaxCalculation::calculateTaxDetails
             * the parent tax gets calculated based on children items
             */
            //
            if (isset($childrenItems[$item->getCode()])) {
                /** @var \Magento\Tax\Api\Data\QuoteDetailsItemInterface $childItem */
                foreach ($childrenItems[$item->getCode()] as $childItem) {
                    $line = $this->interactionLine->getLine($childItem);
                    if ($line) {
                        $lines[] = $line;
                    }
                }
            } else {
                $line = $this->interactionLine->getLine($item);
                if ($line) {

                    /**
                     * The Magento Core does not have the necessary details in the QuoteDetailsItem
                     * which are returned from the call to getItems() above in order to determine if
                     * the shipping type item has a discount or not as it is built differently than other
                     * product type items that include a discountAmount with the item. We can however
                     * determine this by examining the ShipmentAssignment that happens to store the
                     * details of the shipping calculation that occurred earlier in other collect totals.
                     */

                    // Check if we should adjust for a shipping discount amount
                    if ($this->isShippingDiscountAmountAdjustmentNeeded($shippingAssignment, $item, $line)) {

                        // Get the shipping discount amount from the address
                        $shippingDiscountAmount = $shippingAssignment->getShipping()->getAddress()->getShippingDiscountAmount();

                        // Recalculate the line amount with the shipping discount amount included
                        $amountAfterDiscount = ($item->getUnitPrice() * $item->getQuantity()) - $shippingDiscountAmount;

                        // Adjust the line amount
                        $line->setAmount($amountAfterDiscount);
                    }

                    $lines[] = $line;
                }
            }
        }

        // Shipping Address not documented in the interface for some reason
        // they do have a constant for it but not a method in the interface
        //
        // If quote is virtual, getShipping will return billing address, so no need to check if quote is virtual
        $shippingAddress = $shippingAssignment->getShipping()->getAddress();
        $address = $this->address->getAddress($shippingAddress);

        $store = $this->storeRepository->getById($quote->getStoreId());
        $currentDate = $this->getFormattedDate($store);

        // Quote created/updated date is not relevant, so just pass the current date
        $docDate = $currentDate;

        $customerUsageType = $quote->getCustomer()
            ? $this->taxClassHelper->getAvataxTaxCodeForCustomer($quote->getCustomer())
            : null;
        
        if(isset($_COOKIE['is_pickup']) && $_COOKIE['is_pickup'] === 'true'){
            $address = $this->address->getAddress($this->config->getOriginAddress($store));
        }
        
        return [
            'StoreId' => $store->getId(),
            'Commit' => false, // quotes should never be committed
            'CurrencyCode' => $quote->getCurrency()->getQuoteCurrencyCode(),
            'CustomerCode' => $this->getCustomerCode($quote),
            'CustomerUsageType' => $customerUsageType,
            'DestinationAddress' => $address,
            'DocCode' => self::AVATAX_DOC_CODE_PREFIX . $quote->getId(),
            'DocDate' => $docDate,
            'DocType' => DocumentType::$SalesOrder,
            'ExchangeRate' => $this->getExchangeRate($store,
                $quote->getCurrency()->getBaseCurrencyCode(), $quote->getCurrency()->getQuoteCurrencyCode()),
            'ExchangeRateEffDate' => $currentDate,
            'Lines' => $lines,
            // This level of detail is needed in order to receive lines back in response
            'DetailLevel' => DetailLevel::$Line,
            'PurchaseOrderNo' => $quote->getReservedOrderId(),
            'IsSellerImporterOfRecord' => $this->config->isSellerImporterOfRecord(
                $this->config->getOriginAddress($store),
                $address,
                $store
            ),
        ];
    }    
}
