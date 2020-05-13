<?php

namespace Earthlite\EstimatedShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Earthlite\EstimatedShipping\Model\Shipping\Estimation;

class EstimateShipping extends AbstractHelper
{

    protected $shippingEstimation;

    public function __construct(        
        Estimation $shippingEstimation
    ) {
            
        $this->shippingEstimation = $shippingEstimation;
    }

    public function getShippingInfo($sku)
    {
        return $this->shippingEstimation->getEstimatedShipping($sku);   
    }
}