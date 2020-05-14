<?php

namespace Earthlite\Rma\Plugin;

use Magento\Rma\Helper\Data;

/**
 * This class will add Custom Value shipping carrier on storefront for RMA.
 */
class ShippingCarrier
{
    /**
     * Get array of shipping carriers for RMA.
     * Additionally adding Custom Value in the shipping carrier array for RMA.
     *
     * @param Data $subject
     * @param array $result
     * @return array
     */
    public function afterGetShippingCarriers(
        Data $subject,
        $result
    ) {
        $result['custom'] = 'Custom Value';

        return $result;
    }
}
