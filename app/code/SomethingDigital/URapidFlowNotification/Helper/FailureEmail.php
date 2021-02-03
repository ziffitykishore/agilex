<?php

namespace SomethingDigital\URapidFlowNotification\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FailureEmail extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
    }

    public function sendEmail($profileId, $message)
    {
        $emails = explode(",", $this->scopeConfig->getValue('urapidflow/general/email'));
        foreach($emails as $email) {
            try {
                $templateId = $this->scopeConfig->getValue('urapidflow/general/template');

                $this->inlineTranslation->suspend();
                $sender = [
                    'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                    'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
                ];
                $transport =  $this->transportBuilder
                    ->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars([
                        'profileId'  => $profileId,
                        'time' => date('Y-m-d H:i:s'),
                        'message' => $message
                    ])
                    ->setFrom($sender)
                    ->addTo($email)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }
}
