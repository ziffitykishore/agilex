<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Model;

class Logger extends \Monolog\Logger
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Logger\HandlerFactory
     */
    protected $handlerFactory;

    /**
     * @var string
     */
    const DEFAULT_LOG_PATH = '/var/log/rocketweb_shoppingfeeds.log';

    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Logger\HandlerFactory $handlerFactory,
        $logPrefix = 'RSF',
        array $handlers = array(),
        array $processors = array()
    )
    {
        $this->handlerFactory = $handlerFactory;
        parent::__construct($logPrefix, $handlers, $processors);

        // Clear all handlers, we don't want to write to system.log
        $this->handlers = [];
        $this->handlers[] = $this->handlerFactory->create(self::DEFAULT_LOG_PATH, \Monolog\Logger::ERROR);
    }

    /**
     * Adds log handler (file to log into)
     *
     * @param $path
     * @param int $level
     * @return $this
     */
    public function addHandler($path, $level = \Monolog\Logger::INFO)
    {
        $path = '/' . ltrim($path, '/');

        // Add specified handler
        $this->handlers[] = $this->handlerFactory->create($path, $level);

        return $this;
    }

    /**
     * Sets log handler only to default + given
     *
     * @param $path
     * @param int $level
     */
    public function setHandler($path, $level = \Monolog\Logger::INFO)
    {
        while (count($this->handlers) > 1) {
            array_pop($this->handlers);
        }

        $this->addHandler($path, $level);
    }

    public function resetHandler()
    {
        $this->handlers = array();
        return $this;
    }
}