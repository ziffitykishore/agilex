<?php

namespace SomethingDigital\Order\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
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
        ScopeConfigInterface $scopeConfig,
        OrderSender $sender
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
        $this->sender = $sender;
    }

    public function sendEmail($order, $response)
    {
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_sx_order_api_error')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'orderNumber'  => $order->getIncrementId(),
                    'statusCode'  => $response['status'],
                    'errorMessage'  => $response['body'] ?? '',
                ])
                ->setFrom($sender)
                ->addTo($this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE))
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    public function sendSuccessEmail($order)
    {
        try {
            $templateId = $this->scopeConfig->getValue('sales/sx_order/email_template');
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'order' => $order,
                    'billing' => $order->getBillingAddress(),
                    'payment_html' => $this->sender->getPaymentHtml($order),
                    'store' => $order->getStore(),
                    'formattedShippingAddress' => $this->sender->getFormattedShippingAddress($order),
                    'formattedBillingAddress' => $this->sender->getFormattedBillingAddress($order),
                ])
                ->setFrom($sender)
                ->addTo($order->getCustomerEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}