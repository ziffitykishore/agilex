<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-11-08T12:37:53+00:00
 * File:          app/code/Xtento/SavedCc/Observer/DataAssignObserver.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Model\InfoInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    const MODEL_CODE = 'payment_model';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * DataAssignObserver constructor.
     *
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(\Magento\Framework\Encryption\EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @var array
     */
    private $ccKeys = [
        'cc_number',
        'cc_type',
        'cc_exp_year',
        'cc_exp_month',
        'cc_last_4',
        'cc_cid'
    ];

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $dataObject = $this->readDataArgument($observer);

        $additionalData = $dataObject->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $ccData = array_intersect_key($additionalData, array_flip($this->ccKeys));
        if (isset($ccData['cc_number'])) {
            $ccData['cc_last_4'] = substr($ccData['cc_number'], -4);
            $ccData['cc_number_enc'] = $this->encryptor->encrypt($ccData['cc_number']);
        }
        if (isset($ccData['cc_cid'])) {
            // Removed for security reasons
            //$ccData['cc_cid_enc'] = $this->encryptor->encrypt($ccData['cc_cid']);
        }

        //$paymentModel = $this->readPaymentModelArgument($observer);
        $paymentModel = $this->readMethodArgument($observer)->getInfoInstance(); // 2.0 compatibility

        $savedCcData = $ccData;
        unset($savedCcData['cc_number']);
        unset($savedCcData['cc_cid']);

        foreach ($savedCcData as $ccKey => $ccValue) {
            $paymentModel->setAdditionalInformation($ccKey, $ccValue);
        }

        // CC data should be stored explicitly
        foreach ($ccData as $ccKey => $ccValue) {
            $paymentModel->setData($ccKey, $ccValue);
        }
    }
}
