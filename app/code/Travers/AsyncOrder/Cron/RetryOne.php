<?php

namespace Travers\AsyncOrder\Cron;

use Travers\AsyncOrder\Helper\Data;
use SomethingDigital\Order\Model\OrderPlaceApi;

class RetryOne
{
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Data $helper,
        OrderPlaceApi $orderPlaceApi
    ) {
        $this->orderCollection = $orderCollection;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->helper =$helper;
        $this->orderPlaceApi = $orderPlaceApi;
    }

	public function execute()
	{
        try{
            $collection = $this->orderCollection->create();
            $collection->addFieldToFilter('sx_integration_status', 'failed');
            $collection->addFieldToFilter('sx_retry_count', 4);
            foreach ($collection as $order) {
                $this->helper->logData("Order sync retry triggered for Magento order id : ".$order->getId().".Retry count : ".$order->getSxRetryCount());
                $this->sendSync($order);
                $order->setSxRetryCount(3);
                $order->save();
            }

            return $this;
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }

    }
    
    public function sendSync($order)
    {
        $this->orderPlaceApi->sendOrder($order);
    }
}