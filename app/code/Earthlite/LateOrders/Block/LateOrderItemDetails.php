<?php
declare(strict_types = 1);
namespace Earthlite\LateOrders\Block;

use Earthlite\LateOrders\Model\LateOrders as LateOrdersHelper;

/**
 * @Todo Need to implement logic to add dynamic data to email template based on design
 * 
 * class LateOrderItemDetails
 */
class LateOrderItemDetails extends \Magento\Framework\View\Element\Template
{
    /**
     * 
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }
}
