<?php

namespace Ziffity\PickupCheckout\Plugin;

class Configurable
{
    public function afterIsVirtual(\Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject, $result)
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
