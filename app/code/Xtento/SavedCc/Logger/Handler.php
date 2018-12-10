<?php

/**
 * Product:       Xtento_SavedCc (1.0.7)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-09-18T14:51:41+00:00
 * Last Modified: 2017-08-09T13:04:33+00:00
 * File:          app/code/Xtento/SavedCc/Logger/Handler.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
namespace Xtento\SavedCc\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/xtento_savedcc.log';
}