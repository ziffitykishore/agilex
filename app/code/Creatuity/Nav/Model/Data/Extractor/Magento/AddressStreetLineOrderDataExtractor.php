<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Sales\Api\Data\OrderInterface;

class AddressStreetLineOrderDataExtractor extends AddressFieldOrderDataExtractor
{
    protected $lineNumber;

    public function __construct(
        $addressType,
        $lineNumber = 1
    ) {
        parent::__construct($addressType, 'getStreetLine');
        $this->lineNumber = $lineNumber;
    }

    public function extract(OrderInterface $order)
    {
        return $this->getAddress($order)->{$this->accessorMethod}($this->lineNumber);
    }
}
