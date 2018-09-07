<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-08-12T19:11:03+00:00
 * File:          app/code/Xtento/SavedCc/Block/Info.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Block;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;
use Xtento\SavedCc\Helper\Module;

class Info extends ConfigurableInfo
{
    protected $_template = 'Xtento_SavedCc::info/default.phtml';

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * Info constructor.
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param Encryptor $encryptor
     * @param AuthorizationInterface $authorization
     * @param Registry $registry
     * @param Module $moduleHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        Encryptor $encryptor,
        AuthorizationInterface $authorization,
        Registry $registry,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $config, $data);
        $this->encryptor = $encryptor;
        $this->authorization = $authorization;
        $this->moduleHelper = $moduleHelper;
    }

    protected function _prepareSpecificInformation($transport = null)
    {
        if ($transport === null) {
            $transport = new \Magento\Framework\DataObject();
        }
        // Add combined card type
        $ccType = $this->getInfo()->getAdditionalInformation('cc_type') ? $this->getInfo()->getAdditionalInformation('cc_type') : '';
        $ccLastFour = $this->getInfo()->getAdditionalInformation('cc_last_4') ? $this->getInfo()->getAdditionalInformation('cc_last_4') : '';
        $transport->setData((string)__('Type / Last 4'), sprintf('%s (%s)', $ccType, $ccLastFour));
        // Add combined valid until
        $validUntilMonth = $this->getInfo()->getAdditionalInformation('cc_exp_month');
        $validUntilYear = $this->getInfo()->getAdditionalInformation('cc_exp_year');
        if (!empty($validUntilMonth) && !empty($validUntilYear)) {
            $transport->setData((string)__('Expiration Date'), sprintf('%s/%s', $validUntilMonth, $validUntilYear));
        }
        // Get other fields
        $transport = parent::_prepareSpecificInformation($transport);
        // Hide certain fields
        foreach ($transport->getData() as $key => $value) {
            if (strstr($key, 'HIDDEN') !== false) {
                $transport->unsetData($key);
            }
        }
        return $transport;
    }

    /**
     * Returns label
     *
     * @param string $field
     *
     * @return Phrase
     */
    protected function getLabel($field)
    {
        switch ($field) {
            case 'cc_type':
                return __('Type') . 'HIDDEN';
            case 'cc_exp_month':
                return __('Valid Until (Month)') . 'HIDDEN';
            case 'cc_exp_year':
                return __('Valid Until (Year)') . 'HIDDEN';
            case 'cc_last_4':
                return __('Last 4') . 'HIDDEN';
            case 'cc_cid_enc':
                return __('CVC/CVV2');
            case 'cc_number_enc':
                return __('Number');
            default:
                return __($field);
        }
    }

    protected function getValueView($field, $value)
    {
        if ($field == 'cc_last_4') {
            $value = sprintf('xxxx-%s', $value);
        }
        if ($field == 'cc_number_enc' || $field == 'cc_cid_enc') {
            $value = "hidden_" . $this->encryptor->decrypt($value);
        }
        return $value;
    }

    public function showCreditCardInfoInBackend()
    {
        return $this->moduleHelper->confirmEnabled(true) && $this->_scopeConfig->isSetFlag('xtsavedcc/general/show_cc_info_backend');
    }

    public function showWipeButtonInBackend()
    {
        if (empty($this->getInfo()->getAdditionalInformation('cc_number_enc'))) {
            return false;
        }
        return $this->moduleHelper->isModuleEnabled() && $this->_scopeConfig->isSetFlag('xtsavedcc/general/show_wipe_button_backend');
    }

    public function showMaskedCreditCardInfoInBackend()
    {
        return $this->_scopeConfig->isSetFlag('xtsavedcc/general/show_cc_info_masked');
    }

    public function isAllowedToSeeCreditCardInfo()
    {
        return $this->authorization->isAllowed('Xtento_SavedCc::showCcInfo');
    }

    public function isAllowedToWipeCreditCardInfo()
    {
        return $this->authorization->isAllowed('Xtento_SavedCc::wipeCcInfo');
    }

    public function getWipeUrl()
    {
        return $this->_urlBuilder->getUrl('xtento_savedcc/payment/wipe', ['order_id' => $this->getInfo()->getOrder()->getId()]);
    }
}
