<?php

namespace PartySupplies\PalletShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class Shipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $code = 'palletshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\GuestCart\GuestShippingMethodManagement $guestCart,
        \Magento\Quote\Model\ShippingMethodManagement $shippingMethod,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->shippingMethodManager = $shippingMethod;
        $this->_checkoutSession = $checkoutSession;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->guestCart = $guestCart;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->code => $this->getConfigData('name')];
    }

    /**
     *
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     *
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $quote = $this->_checkoutSession->getQuote();
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!$this->isPalletValidation()) {
            return false;
        }

        $result = $this->rateResultFactory->create();
        $rates = $result->getRatesByCarrier("UPS");

        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->code);
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->getShippingPrice();

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     *
     * @return boolean
     */
    public function isPalletValidation()
    {
        $rate = $this->_checkoutSession->getShippingRate();
        if ($rate) {
            $upsGroundThreshold = $this->getConfigData('ups_price_threshold');
            if ($rate > $upsGroundThreshold) {
                return true;
            }
        }
        return false;
    }
}
