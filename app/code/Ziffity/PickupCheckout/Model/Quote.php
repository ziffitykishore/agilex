<?php

namespace Ziffity\PickupCheckout\Model;

class Quote
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

    public function afterIsVirtual(\Magento\Quote\Model\Quote $subject, $result)
    {

        $allowAction = !in_array($this->httpRequest->getActionName(), $this->isAllow());

        if (isset($_COOKIE["is_pickup"]) && $_COOKIE["is_pickup"] == "true" && $allowAction) {
            $result = true;
            return $result;
        }

    }

    protected function isAllow()
    {
        return ['couponPost'];
    }
}
