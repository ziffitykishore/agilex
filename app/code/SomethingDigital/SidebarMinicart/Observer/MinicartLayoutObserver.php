<?php

namespace SomethingDigital\SidebarMinicart\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Store\Model\StoreManagerInterface;


class MinicartLayoutObserver implements ObserverInterface
{

    protected $_storeManager;

    /**
     * MagezinerLoadBeforeObserver constructor.
     * @param MagezinerBlock $magezinerBlock
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig($config_path)
    {
        $store = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getEvent()->getData('layout');
      
        /* Only load Layout Handle IF sidebar minicart is enabled */
        if ($this->getConfig('checkout/sidebar/display') && $this->getConfig('checkout/sidebar/enable_minicart_sidebar')) {
            $layout->getUpdate()->addHandle('minicart_flyout');
        }
    }
}