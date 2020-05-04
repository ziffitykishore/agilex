<?php
declare(strict_types = 1);
namespace Earthlite\OrderComments\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * class Comments
 */
class Comments extends View
{
    const XML_PATH_ORDER_COMMENTS_TITLE = 'earthlite_checkout/ordercomments/title';
    
    /**
     * 
     * @return string
     */
    public function getOrderCommentsTitle()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ORDER_COMMENTS_TITLE); //you get your value here

    }
    
    /**
     * 
     * @return string
     */
    public function getComments()
    {
        return $this->getOrder()->getOrderComments();
    }
}
