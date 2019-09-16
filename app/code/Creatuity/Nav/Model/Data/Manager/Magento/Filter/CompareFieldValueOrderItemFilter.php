<?php

namespace Creatuity\Nav\Model\Data\Manager\Magento\Filter;

use Magento\Sales\Api\Data\OrderItemInterface;

class CompareFieldValueOrderItemFilter implements OrderItemFilterInterface
{
    protected $accessorMethod;
    protected $comparisonOperator;
    protected $comparisonValue;

    public function __construct(
        $accessorMethod,
        $comparisonOperator,
        $comparisonValue
    ) {
        $this->accessorMethod = $accessorMethod;
        $this->comparisonOperator = $comparisonOperator;
        $this->comparisonValue = $comparisonValue;
    }

    public function isFiltered(OrderItemInterface $orderItem)
    {
        switch ($this->comparisonOperator) {
            case 'equal':
                return $orderItem->{$this->accessorMethod}() === $this->comparisonValue;

            case 'not-equal':
                return $orderItem->{$this->accessorMethod}() !== $this->comparisonValue;
        }

        throw new \Exception("Comparison operator '{$this->comparisonOperator}' is NOT valid'");
    }
}
