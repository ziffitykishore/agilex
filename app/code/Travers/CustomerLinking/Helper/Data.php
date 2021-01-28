<?php
declare(strict_types=1);

namespace Travers\CustomerLinking\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Data
{
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $email
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->email = $email;
    }

    public function getConfigValue($path) 
    {
        return $this->scopeConfig->getValue($path);
    }

    public function logData($message = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_account_linking.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($message, true));
    }

    public function sendMail($message)
    {
        try {var_dump($message);
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
            ];
            $transport = $this->email
                ->setTemplateIdentifier('customer_account_linking_error')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'Message'  => $message
                ])
                ->setFrom($sender)
                ->addTo($this->scopeConfig->getValue('sx/customer_linking/failure_email'))
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
    
}