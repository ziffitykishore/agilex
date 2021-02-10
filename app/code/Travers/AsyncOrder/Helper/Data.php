<?php
declare(strict_types=1);

namespace Travers\AsyncOrder\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $email,
        StateInterface $inlineTranslation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->email = $email;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function getConfigValue($path) 
    {
        return $this->scopeConfig->getValue($path);
    }

    public function logData($message = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/async_order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($message, true));
    }
}