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


    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $source
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->sourceCollection = $source;
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $selectedLocation = isset($_COOKIE['storeLocation']) ? json_decode($_COOKIE['storeLocation'],true) : null;

        if($selectedLocation) {
            $selectedLocation = $this->sourceCollection->addFieldToFilter('enabled',1)->addFieldToFilter('source_code',$selectedLocation["code"])->load()->getFirstItem();
            $adminEmail = $selectedLocation->getEmail();
            $adminName = $selectedLocation->getContactName();
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