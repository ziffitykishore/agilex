<?php
namespace Earthlite\ProductAlert\Block\Product\View;

/**
 * class stock
 */
class Stock extends \Magento\ProductAlert\Block\Product\View
{
    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (!$this->_helper->isStockAlertAllowed() || !$this->getProduct() || $this->getProduct()->isAvailable()) {
            if($this->getProduct()->getTypeId() != 'configurable') {
               $template = '';
            }
        } 
        return parent::setTemplate($template);
    }
}
