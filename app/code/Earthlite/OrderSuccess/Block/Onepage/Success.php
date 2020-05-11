<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Earthlite\OrderSuccess\Block\Onepage;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Address\Config as addressConfig;

/**
 * One page checkout success page
 *
 * @api
 * @since 100.0.2
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var AddressRenderer
     */
    protected $_addressConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Sales\Model\Order\Config                $orderConfig
     * @param \Magento\Framework\App\Http\Context              $httpContext
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        addressConfig $_addressConfig,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_addressConfig = $_addressConfig;
    }

    public function getOrder() 
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /**
     * Returns string with formatted address
     *
     * @param  Address $address
     * @return null|string
     */
    public function _getAddressHtml($address)
    {
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($address);        
    }
}
