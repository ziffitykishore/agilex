<?php

namespace SomethingDigital\Order\Plugin;

use Magento\Checkout\Controller\Onepage\Success;
use Magento\Checkout\Model\Session;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\MessageQueue\PublisherInterface;


class SendOrderToAPI
{
    const RETRANS_PUBLISHER_TOPIC = 'async_order';

    protected $checkoutSession;
    protected $logger;
    protected $orderPlaceApi;

    public function __construct(
        Session $checkoutSession,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        OrderPlaceApi $orderPlaceApi,
        PublisherInterface $publisher
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->publisher = $publisher;
    }

    /**
     *Send order to API
     *
     * @param Success $subject
     * @param $result
     *
     * @return array
     */
    public function afterExecute(Success $subject, $result)
    {
        $order = $this->checkoutSession->getLastRealOrder();

        try {
            if(!$this->scopeConfig->getValue('async_order/general/enable'))
                $this->orderPlaceApi->sendOrder($order);
            else
                $this->publisher->publish(self::RETRANS_PUBLISHER_TOPIC, $order->getId());
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }

        return $result;
    }
}
