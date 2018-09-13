<?php

/**
 * Product:       Xtento_OrderExport (2.6.2)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-08-15T13:45:52+00:00
 * Last Modified: 2016-03-01T16:04:39+00:00
 * File:          app/code/Xtento/OrderExport/Logger/Handler.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
namespace Xtento\OrderExport\Logger;

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
    protected $fileName = '/var/log/xtento_orderexport.log';
}