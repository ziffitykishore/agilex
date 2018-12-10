<?php

/**
 * Product:       Xtento_SavedCc (1.0.7)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-09-18T14:51:41+00:00
 * Last Modified: 2017-08-12T14:36:24+00:00
 * File:          app/code/Xtento/SavedCc/Model/Ui/ConfigProvider.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Gateway\Config\Config;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'xtsavedcc';

    /**
     * @var Config
     */
    private $config;

    /**
     * ConfigProvider constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                ],
            ]
        ];
    }
}
