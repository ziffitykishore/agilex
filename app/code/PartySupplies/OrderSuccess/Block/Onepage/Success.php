<?php

/**
 *
 * Get the Order Items
 *
 */

namespace PartySupplies\OrderSuccess\Block\Onepage;

use Magento\Checkout\Model\Session;

/**
 * Success Class
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * Constructor Modification
     *
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        Session $checkoutSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->_countryFactory = $countryFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );
    }
    /**
     * Get the Order Items
     *
     * @return void
     */
    public function getItems()
    {
        $orderInfo = $this->_checkoutSession->getLastRealOrder();
        return $this->orderFactory->create()->load($orderInfo->getEntityId());
    }

    /**
     * Format the given price
     *
     * @param int|string $price
     *
     * @return int|string
     */
    public function formatPricing($price = null)
    {
        return $this->pricingHelper->currency((float)$price, true, false);
    }

    public function getCountryname($countryCode){    
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    
}
