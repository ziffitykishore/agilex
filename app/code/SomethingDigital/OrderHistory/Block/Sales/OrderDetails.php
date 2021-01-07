<?php

namespace SomethingDigital\OrderHistory\Block\Sales;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class OrderDetails extends Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Http\Context
     * @since 100.2.0
     */
    private $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HttpContext $httpContext,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Order # %1', $this->getOrder()->getData('SxId')));
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Framework\DataObject
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sx_current_order');
    }

    /**
     * Calculate order grand total
     *
     * @return float
     */
    public function getOrderGrandTotal($order)
    {
        $items = $order->getData('LineItems');
        $total = 0;
        foreach ($items as $key => $item) {
            $total += ($item['SoldPrice']*$item['Qty']);
        }
        $total += $order->getData('ShipFee');
        $total += $order->getData('Tax');

        return $total;
    }

    /**
     * Calculate subtotal
     *
     * @return float
     */
    public function getOrderSubtotal($order)
    {
        $items = $order->getData('LineItems');
        $total = 0;
        foreach ($items as $key => $item) {
            $total += ($item['SoldPrice']*$item['Qty']);
        }

        return $total;
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }
        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
    }

    /**
     * Get current store currency symbol with price
     */
    public function getCurrencyFormat($price)
    {
        $price = $this->priceCurrency->format($price,true,2);
        return $price;
    }
}
