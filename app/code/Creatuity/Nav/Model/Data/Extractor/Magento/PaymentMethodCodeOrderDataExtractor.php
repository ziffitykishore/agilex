<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Creatuity\Nav\Model\Data\Mapping\BinaryMapping;
use Magento\Sales\Api\Data\OrderInterface;

class PaymentMethodCodeOrderDataExtractor implements OrderDataExtractorInterface
{
    protected $paymentMethodCodeMap;

    public function __construct(array $paymentMethodCodeMap)
    {
        foreach ($paymentMethodCodeMap as $mapping) {
            $this->addPaymentMethodCodeMapping($mapping);
        }
    }

    public function extract(OrderInterface $order)
    {
        $paymentMethodCode = $order->getPayment()->getMethod();
        return $this->paymentMethodCodeMap[$paymentMethodCode] ?? $paymentMethodCode;
    }

    protected function addPaymentMethodCodeMapping(BinaryMapping $mapping)
    {
        $this->paymentMethodCodeMap[$mapping->getFrom()] = $mapping->getTo();
    }
}
