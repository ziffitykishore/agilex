<?php

namespace SomethingDigital\Order\Plugin;

use Magento\Sales\Block\Order\Totals;

/**
 * Class RemoveBaseGrandtotal
 */
class RemoveBaseGrandtotal
{
    /**
     * Remove base_grandtotal which shows price in $ on Canada store.
     *
     * @param Totals $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetTotals(Totals $subject, $result)
    {
        foreach ($result as $key => $total) {
            if ($total->getCode() == 'base_grandtotal') {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
