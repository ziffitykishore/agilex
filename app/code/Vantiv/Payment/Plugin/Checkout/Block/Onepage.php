<?php
/**
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vantiv\Payment\Plugin\Checkout\Block;

class Onepage
{
    /**
     * @var \Vantiv\Payment\Helper\Recurring
     */
    private $recurringHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Vantiv\Payment\Helper\Recurring $recurringHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Vantiv\Payment\Helper\Recurring $recurringHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->recurringHelper = $recurringHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Remove gift card input if cart contains subscription items
     *
     * @param \Magento\Checkout\Block\Onepage $subject
     * @param string $result
     * @return string
     */
    public function afterGetJsLayout(\Magento\Checkout\Block\Onepage $subject, $result)
    {
        $quote = $this->checkoutSession->getQuote();
        if ($quote && $this->recurringHelper->quoteContainsSubscription($quote)) {
            $jsLayout = \Zend_Json::decode($result);
            if (isset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['giftCardAccount']
            )) {
                unset(
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['afterMethods']['children']['giftCardAccount']
                );
                $result = \Zend_Json::encode($jsLayout);
            }
        }

        return $result;
    }
}
