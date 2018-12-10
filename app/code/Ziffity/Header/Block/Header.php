<?php

namespace Ziffity\Header\Block;

class Header extends \Magento\Framework\View\Element\Template
{

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function getConfigValue()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeInfo = [];
        $storeInfo['welcome_header'] = $this->_scopeConfig->getValue('design/header/welcome', $storeScope);
        $storeInfo['store_phone'] = $this->_scopeConfig->getValue('general/store_information/phone', $storeScope);
        $storeInfo['store_timings'] = $this->_scopeConfig->getValue('general/store_information/hours', $storeScope);
        return $storeInfo;
    }

    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }

}
