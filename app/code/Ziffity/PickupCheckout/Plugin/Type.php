<?php

namespace Ziffity\PickupCheckout\Plugin;

class Type
{
    public function afterIsVirtual(\Magento\Bundle\Model\Product\Type $subject, $result)
    {

        if (isset($_COOKIE["is_pickup"]) && $_COOKIE["is_pickup"] == "true") {
            $result = true;
            return $result;
        } else {
            $result = false;
            return $result;            
        }
    }
}
