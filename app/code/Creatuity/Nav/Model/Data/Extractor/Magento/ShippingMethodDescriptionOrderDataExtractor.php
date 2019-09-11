<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;

class ShippingMethodDescriptionOrderDataExtractor implements OrderDataExtractorInterface
{
    protected $scopeConfig;
    protected $carrierCodeFormatterFactoryMappings;
    protected $defaultShippingMethodDescription;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $carrierCodeFormatterFactoryMappings,
        $defaultShippingMethodDescription
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->carrierCodeFormatterFactoryMappings = $carrierCodeFormatterFactoryMappings;
        $this->defaultShippingMethodDescription = $defaultShippingMethodDescription;
    }

    public function extract(OrderInterface $order)
    {
        $carrierCode = $this->getCarrierCode($order);

        if (!isset($this->carrierCodeFormatterFactoryMappings[$carrierCode])) {
            return $this->defaultShippingMethodDescription;
        }

        return $this->getShippingMethodDescription($order, $carrierCode);
    }

    protected function getShippingMethodDescription(OrderInterface $order, $carrierCode)
    {
        return $this->carrierCodeFormatterFactoryMappings[$carrierCode]
            ->create([
                'carrierTitle'        => $this->getCarrierTitle($carrierCode),
                'shippingDescription' => $order->getShippingDescription(),
            ])
            ->format()
        ;
    }

    protected function getCarrierCode(OrderInterface $order)
    {
        return $order->getShippingMethod(true)->getCarrierCode();
    }

    protected function getCarrierTitle($carrierCode)
    {
        return $this->scopeConfig->getValue("carriers/{$carrierCode}/title");
    }
}
