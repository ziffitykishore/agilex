<?php
declare(strict_types=1);

namespace Travers\AsyncOrder\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use SomethingDigital\Order\Model\OrderPlaceApi;
use Travers\AsyncOrder\Helper\Data;

class Consumer
{
    public function __construct(
        Customer $customerModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        OrderPlaceApi $orderPlaceApi,
        Data $helper
    ) {
        $this->customer = $customerModel;
        $this->date = $date;
        $this->orderRepository = $orderRepository;
        $this->orderPlaceApi = $orderPlaceApi;
        $this->helper = $helper;
    }

    public function consumeOrderId(String $orderId) {
        $this->helper->logData('Order sync started for Magento order Id : '.$orderId);
        $order = $this->orderRepository->get($orderId);
        try {
            $result = $this->orderPlaceApi->sendOrder($order);
            if($result['status'] == true)
                $this->helper->logData('Order sync successful for Magento order Id : '.$orderId);     
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }
    }
}