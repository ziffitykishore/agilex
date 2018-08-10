<?php
namespace Ziffity\Header\Block;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Header extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = array()
    )
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }
    
    public function getConfigValue(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeInfo = [];
        $storeInfo['welcome_header'] = $this->_scopeConfig->getValue('design/header/welcome',
                $storeScope);
        $storeInfo['store_phone'] = $this->_scopeConfig->getValue('general/store_information/phone',
                $storeScope);
        $storeInfo['store_timings'] = $this->_scopeConfig->getValue('general/store_information/hours',
                $storeScope);
        return $storeInfo;
    }
}
