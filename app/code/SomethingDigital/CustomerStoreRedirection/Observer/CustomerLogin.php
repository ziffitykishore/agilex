<?php

namespace SomethingDigital\CustomerStoreRedirection\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use SomethingDigital\CustomerStoreRedirection\Model\Redirection;

class CustomerLogin implements ObserverInterface
{
    protected $storeManager;
    protected $redirection;

    public function __construct(
        StoreManagerInterface $storeManager,
        Redirection $redirection
    ) {
        $this->storeManager = $storeManager;
        $this->redirection = $redirection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerStoreId = $customer->getStoreId();

        $customerStore = $this->storeManager->getStore($customerStoreId);
        $currectStore = $this->storeManager->getStore();
        if ($customerStore->getId() != $currectStore->getId()) {
            $this->redirection->url = $customerStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'customer/account';
        }
    }
}