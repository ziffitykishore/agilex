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

    public function getStoreConfig($code)
    {
        return $this->shippingEstimation->getConfigGeneral($code);
    }

    public function getItemProductionStatus($code)
    {
        return $this->shippingEstimation->getItemProductionStatus($code);        
    }

    public function getCartItemStatus()
    {
        return $this->shippingEstimation->getCartItemStatus();
    }
}