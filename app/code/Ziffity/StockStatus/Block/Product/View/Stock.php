<?php
/**
 * Override Product Alert Stock View
 *
 */
namespace Ziffity\StockStatus\Block\Product\View;

/**
 * Show product alert view stock
 *
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
        //Get the type of product and apply condition based on it
        $typeId = $this->getProduct()->getTypeId();
        if($typeId == "configurable") { // Is available condition will be applied in the configuration option loading
            $condition = !$this->_helper->isStockAlertAllowed() || !$this->getProduct();
        } else {
            $condition = !$this->_helper->isStockAlertAllowed() || !$this->getProduct() || $this->getProduct()->isAvailable();
        }
        if ($condition) {
            $template = '';
        } else {
            $this->setSignupUrl($this->_helper->getSaveUrl('stock'));
        }
        return parent::setTemplate($template);
    }
}
