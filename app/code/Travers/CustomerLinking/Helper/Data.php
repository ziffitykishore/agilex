<?php
declare(strict_types=1);

namespace Travers\CustomerLinking\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Session\SessionManagerInterface;
use SomethingDigital\CustomerValidation\Model\CustomerApi;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $email,
        StateInterface $inlineTranslation,
        SessionManagerInterface $customerSession,
        CustomerApi $customerApi
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->email = $email;
        $this->inlineTranslation = $inlineTranslation;
        $this->session = $customerSession;
        $this->customerApi = $customerApi;
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

    public function sendMail($message, $customerId)
    {
        try {
            $templateId = $this->scopeConfig->getValue('sx/customer_linking/email_template');
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
            ];
            $transport = $this->email
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'message'  => $message,
                    'customerId' => $customerId
                ])
                ->setFrom($sender)
                ->addTo($this->scopeConfig->getValue('sx/customer_linking/failure_email'))
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logData($e->getMessage());
        }
    }

    public function getSessionAccountId()
    {
        return $this->session->getAccountId();
    }

    public function getSessionZipCode()
    {
        try{
            $traversAccountId = $this->getSessionAccountId();
            if($traversAccountId) {
                $customerApi = $this->customerApi->getCustomer($traversAccountId);
                if($customerApi)
                    return $customerApi['body']['Address']['PostalCode'];
            }
            return '';
        } catch (\Exception $e) {
           $this->logData($e->getMessage());
        }
    }
    
}