<?php

namespace Ziffity\Common\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
 
class OrderPlaceAfter implements ObserverInterface
{

    protected $request;
    
    protected $logger;
 
    protected $transportBuilder;

    protected $storeManager;
    
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getData('transportObject');
        $store = $this->storeManager->getStore()->getId();
        $transport = $this->transportBuilder->setTemplateIdentifier('new_order_admin')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($order->getData())
            ->setFrom('general')
            ->addTo('admin@gogoballoonz.com', 'Admin')
            ->getTransport();
        $transport->sendMessage();
        return $this;        
    }
}