<?php

namespace Earthlite\BackOrders\Block;

use Earthlite\BackOrders\Model\OrderItems;

class BackOrderItemDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * 
     * @return string
     */
    public function getOrderItems()
    {       
        return $this->getItems();
    }
}
