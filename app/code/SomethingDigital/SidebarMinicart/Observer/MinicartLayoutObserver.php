<?php

namespace SomethingDigital\SidebarMinicart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class MinicartLayoutObserver implements ObserverInterface
{
    protected $_scopeConfig;

    /**
     *  MinicartLayoutObserver constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getEvent()->getData('layout');
      
        /* Only load Layout Handle IF sidebar minicart is enabled */
        if ($this->getConfig('checkout/sidebar/display') && $this->getConfig('checkout/sidebar/enable_minicart_sidebar')) {
            $layout->getUpdate()->addHandle('minicart_flyout');
        }
    }
}