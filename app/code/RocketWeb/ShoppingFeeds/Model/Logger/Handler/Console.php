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

namespace RocketWeb\ShoppingFeeds\Model\Logger\Handler;

class Console extends \Monolog\Handler\AbstractProcessingHandler
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    public function __construct()
    {
        parent::__construct();

        $this->setFormatter(new \RocketWeb\ShoppingFeeds\Model\Logger\Formatter\MemoryLog());
        $this->setLevel(\Monolog\Logger::INFO);
    }

    public function setOutputInterface(\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }

    public function write(array $record)
    {
        if (!is_null($this->output) && $this->output instanceof \Symfony\Component\Console\Output\OutputInterface) {
            $this->output->write((string) $record['formatted']);
        }
    }


}