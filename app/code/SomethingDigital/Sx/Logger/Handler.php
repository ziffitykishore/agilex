<?php

namespace SomethingDigital\Sx\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::ALERT;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/sx.log';
}