<?php
namespace SomethingDigital\Order\Block\Checkout;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    protected $order;

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $this->order = $this->_checkoutSession->getLastRealOrder();

        return $this->order;
    }
}