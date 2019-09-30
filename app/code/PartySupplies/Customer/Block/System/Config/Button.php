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
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     *
     *
     */
    protected $_template = 'PartySupplies_Customer::system/config/button.phtml';
 
    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getBaseUrl().'pub/media/reseller_certificate/'.
            $this->_scopeConfig->getValue('reseller_certification/general/upload_form');
    }

    /**
     * @return bool
     */
    public function isFormUploaded()
    {
        $value = $this->_scopeConfig->getValue('reseller_certification/general/upload_form');
        return isset($value) && !empty($value);
    }
    
    /**
     * @return string
     */
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
