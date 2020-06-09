<?php
declare(strict_types = 1);
namespace Earthlite\EstimatedShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Earthlite\EstimatedShipping\Model\Shipping\Estimation;

/**
 * class EstimateShipping
 */
class EstimateShipping extends AbstractHelper
{
    /**
     *
     * @var Estimation 
     */
    protected $shippingEstimation;

    /**
     * 
     * @param Estimation $shippingEstimation
     */
    public function __construct(        
        Estimation $shippingEstimation
    ) {
            
        $this->shippingEstimation = $shippingEstimation;
    }

    /**
     * 
     * @param string $sku
     * @return string
     */
    public function getShippingInfo($sku)
    {
        return $this->shippingEstimation->getEstimatedShipping($sku);   
    }

    /**
     * 
     * @param string $sku
     * @return string
     */
    public function getItemType($sku) 
    {
        return $this->shippingEstimation->getItemType($sku);
    }

    /**
     * 
     * @param string $code
     * @return string
     */
    public function getStoreConfig($code)
    {
        return $this->shippingEstimation->getConfigGeneral($code);
    }
    
    /**
     * 
     * @param string $code
     * @return string
     */
    public function getItemProductionStatus($code)
    {
        return $this->shippingEstimation->getItemProductionStatus($code);        
    }

    /**
     * 
     * @return string
     */
    public function getCartItemStatus()
    {
        return $this->shippingEstimation->getCartItemStatus();
    }
}