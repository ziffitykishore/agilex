<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PartySupplies\Customer\Block\System\Config;

/**
 * Description of Button
 *
 * @author linux
 */
class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'PartySupplies_Customer::system/config/button.phtml';
 
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
    public function getAjaxUrl()
    {
        return $this->getBaseUrl().'pub/media/reseller_certificate/'.
            $this->_scopeConfig->getValue('reseller_certification/general/upload_form');
    }
    public function isFormUploaded()
    {
        $value = $this->_scopeConfig->getValue('reseller_certification/general/upload_form');
        return isset($value) && !empty($value);
    }
    
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'btnid',
                'label' => __('Verify'),
            ]
        );
 
        return $button->toHtml();
    }
}
