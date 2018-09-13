<?php

/**
 * Product:       Xtento_XtCore (2.3.0)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Helper/Shipping.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Helper;

class Shipping extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function determineCarrierTitle($carrierCode, $shippingMethod = '', $storeId = null)
    {
        if ($carrierCode == 'custom') {
            $carrierTitle = (empty($shippingMethod)) ? __('Custom') : $shippingMethod;
        } else {
            $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            if (empty($carrierTitle)) {
                // Try to get carrier from XTENTO custom carrier trackers extension
                $carrierTitle = $this->scopeConfig->getValue(
                    'customtrackers/' . $carrierCode . '/title',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            }
        }
        return $carrierTitle;
    }
}
