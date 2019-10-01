<?php
namespace PartySupplies\PalletShipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Ups\Helper\Config;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Xml\Security;

/**
 * Carrier
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Carrier extends \Magento\Ups\Model\Carrier
{
    
    /**
     *
     * @param \Magento\Checkout\Model\Session                             $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param Security                                                    $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory            $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory              $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory        $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory       $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory                      $regionFactory
     * @param \Magento\Directory\Model\CountryFactory                     $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory                    $currencyFactory
     * @param \Magento\Directory\Helper\Data                              $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface        $stockRegistry
     * @param \Magento\Framework\Locale\FormatInterface                   $localeFormat
     * @param Config                                                      $configHelper
     * @param ClientFactory                                               $httpClientFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        Config $configHelper,
        ClientFactory $httpClientFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $localeFormat,
            $configHelper,
            $httpClientFactory,
            $data
        );
    }

    /**
     * Collect and get rates/errors
     *
     * @param RateRequest $request
     * @return  Result|Error|bool
     */
    public function collectRates(RateRequest $request)
    {
        $this->setRequest($request);
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }

        $this->setRequest($request);
        $this->_result = $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);

        $rates = $this->_result->asArray();

        $this->setUpsSession($rates);
        
        return $this->getResult();
    }

    /**
     *
     * @param type $rates
     */
    public function setUpsSession($rates)
    {
        if ($rates) {
            $upsTitle = $this->_scopeConfig->getValue(
                "carriers/palletshipping/ups_method",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($rates as $rate) {
                $methods = $rate['methods'];
                foreach ($methods as $method) {
                    $methodTitle = $method['title']->getText();
                    if ($methodTitle == $upsTitle) {
                        $this->_checkoutSession->setShippingRate($method['price']);
                    }
                }
            }
        }
    }
}
