<?php

namespace SomethingDigital\Order\Plugin;

use Magento\Checkout\Controller\Onepage\Success;
use Magento\Checkout\Model\Session;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Psr\Log\LoggerInterface;


class SendOrderToAPI
{
    protected $checkoutSession;
    protected $logger;
    protected $orderPlaceApi;

    public function __construct(
        Session $checkoutSession,
        LoggerInterface $logger,
        OrderPlaceApi $orderPlaceApi
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->orderPlaceApi = $orderPlaceApi;
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
            $this->orderPlaceApi->sendOrder($order);
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }

        return $result;
    }
}
