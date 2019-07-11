<?php

namespace Ziffity\PickupCheckout\Plugin;

class AbstractType
{
    
    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;


    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->httpRequest  = $request;
    }
    
    public function afterIsVirtual(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $result)
    {

        $allowAction = !in_array($this->httpRequest->getActionName(), $this->isAllow());
        
        if (isset($_COOKIE["is_pickup"]) && $_COOKIE["is_pickup"] == "true" && $allowAction) {
            $result = true;
            return $result;
        } else {
            $result = false;
            return $result;            
        }   
    }
    
    protected function isAllow()
    {
        return ['couponPost'];
    }    
    
}
