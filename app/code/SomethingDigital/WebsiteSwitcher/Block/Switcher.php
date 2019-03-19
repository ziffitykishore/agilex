<?php

namespace SomethingDigital\WebsiteSwitcher\Block;

class Switcher extends \Magento\Store\Block\Switcher
{
    public function getWebsites()
    {
       return $this->_storeManager->getWebsites();
    }    
}
