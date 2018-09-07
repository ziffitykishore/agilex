<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2018-04-25T16:58:54+00:00
 * File:          app/code/Xtento/SavedCc/Helper/Module.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Helper;

use Magento\Framework\Exception\LocalizedException;

class Module extends \Xtento\XtCore\Helper\AbstractModule
{
    protected $edition = 'CE';
    protected $module = 'Xtento_SavedCc';
    protected $extId = 'MTWOXtento_SavedCc821321';
    protected $configPath = 'xtsavedcc/general/';

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return parent::isModuleEnabled();
    }

    /**
     * Wipe credit card information for an order
     *
     * @param $order
     *
     * @throws LocalizedException
     *
     * @api
     */
    public function wipeCreditCardInfo($order)
    {
        if (!$order->getId()) {
            throw new LocalizedException(__('Order couldn\'t be loaded.'));
        }
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();
        if ($payment->getMethod() == \Xtento\SavedCc\Model\Ui\ConfigProvider::CODE) {
            $payment->setAdditionalInformation('cc_number_enc', null);
            $payment->setAdditionalInformation('cc_cid_enc', null);
            $payment->setData('cc_number_enc', null);
            $payment->save();
        }
        return $order;
    }
}
