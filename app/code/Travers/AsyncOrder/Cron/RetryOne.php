<?php

namespace Travers\AsyncOrder\Cron;

class RetryOne
{
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->orderCollection = $orderCollection;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

	public function execute()
	{
            $collection = $this->orderCollection->create();
            $collection->addFieldToFilter('sx_integration_status', 'failed');
$this->logData('sdsds');
$this->logData(count($collection));
            foreach ($collection as $order) {
                $this->sendSync($order->getId());
            }

		return $this;
    }
    
    public function sendSync($orderId)
    {$this->logData($orderId);
        $this->logger->info($orderId);
    }

    public function logData($message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/aync_order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($message, true));
    }
}