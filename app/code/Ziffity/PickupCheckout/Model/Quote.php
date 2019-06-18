<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Ziffity\PickupCheckout\Model;

class Quote extends \Magento\Quote\Model\Quote
{
    /**
     * Check quote for virtual product only and pickup order
     *
     * @return bool
     */
    public function isVirtual()
    {
        $isVirtual = true;
        $countItems = 0;
        foreach ($this->getItemsCollection() as $_item) {
            /* @var $_item \Magento\Quote\Model\Quote\Item */
            if ($_item->isDeleted() || $_item->getParentItemId()) {
                continue;
            }
            $countItems++;
            if (!$_item->getProduct()->getIsVirtual()) {
                $isVirtual = false;
                break;
            }
        }
        if (isset($_COOKIE["is_pickup"]) && $_COOKIE["is_pickup"] == "true") {
            return true;
        }
        
        return $countItems == 0 ? false : $isVirtual;
    }
}
