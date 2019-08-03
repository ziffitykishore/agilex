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
    
    protected $sourceCollection;
    
    protected $_modelPos;

    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\PointOfSale\Model\PointOfSale $modelPos
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->_modelPos = $modelPos;
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        if(isset($storeId)) {
            $pos = $this->_modelPos->getPlacesByStoreId($storeId, true);
            foreach ($pos as $place) {
                $adminEmail = $place->getEmail();
                $adminName = $place->getName();
            }
        }
        $order = $observer->getData('transportObject');
        $store = $this->storeManager->getStore()->getId();
        $transport = $this->transportBuilder->setTemplateIdentifier('new_order_admin')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($order->getData())
            ->setFrom('general')
            ->addTo($adminEmail, $adminName)
            ->getTransport();
        $transport->sendMessage();
        return $this;
    }
}