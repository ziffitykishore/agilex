<?php

namespace SomethingDigital\Order\Plugin\Sales\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class Button
{
   public function beforeSetLayout(OrderView $subject)
   {

       $subject->addButton(
           'order_resend_to_api_button',
           [
               'label' => __('Re-send order to API'),
               'class' => __('resend-to-api-button'),
               'id' => 'order-view-resend-to-api-button',
               'onclick' => 'setLocation(\'' . $subject->getUrl('sxresendorder/order') . '\')'
           ]
       );
   }
}
