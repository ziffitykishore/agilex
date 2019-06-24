<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Ziffity\PickupCheckout\Model;

class Quote 
{
    public function afterIsVirtual(\Magento\Quote\Model\Quote $subject, $result)
    {
        if (isset($_COOKIE["is_pickup"]) && $_COOKIE["is_pickup"] == "true") {
            $result = true;
        }
        return $result;
    }
}
