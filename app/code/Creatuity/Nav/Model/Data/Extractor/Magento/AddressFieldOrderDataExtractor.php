<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address;

class AddressFieldOrderDataExtractor implements OrderDataExtractorInterface
{
    protected $addressType;
    protected $accessorMethod;

    public function __construct($addressType, $accessorMethod)
    {
        $this->addressType = $addressType;
        $this->accessorMethod = $accessorMethod;
    }

    public function extract(OrderInterface $order)
    {
        return $this->getAddress($order)->{$this->accessorMethod}();
    }

    protected function getAddress(OrderInterface $order)
    {
        switch ($this->addressType) {
            case Address::TYPE_BILLING:
                return $order->getBillingAddress();

            case Address::TYPE_SHIPPING:
                return $order->getShippingAddress();
        }

        throw new \Exception("Address type '{$this->addressType}' is NOT valid");
    }
}
