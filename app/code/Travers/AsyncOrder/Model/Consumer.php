<?php
declare(strict_types=1);

namespace Travers\AsyncOrder\Model;

use Travers\CustomerLinking\Model\CustomerSync;
use Travers\CustomerLinking\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use SomethingDigital\Order\Model\OrderPlaceApi;

class Consumer
{
    public function __construct(
        CustomerSync $customerApi,
        Data $data,
        Customer $customerModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        OrderPlaceApi $orderPlaceApi
    ) {
        $this->customerApi = $customerApi;
        $this->helper = $data;
        $this->customer = $customerModel;
        $this->date = $date;
        $this->orderRepository = $orderRepository;
        $this->orderPlaceApi = $orderPlaceApi;
    }

    public function consumeOrderId(String $orderId) {
        var_dump($orderId);
        $count = (int)$this->helper->getConfigValue('sx/customer_linking/retry_count');
        $today = $this->date->gmtDate();
        $order = $this->orderRepository->get($orderId);
        try {
           $this->orderPlaceApi->sendOrder($order);
        }
        catch(\Exception $e) {
            $this->helper->logData($e->getMessage());
        }
    }
}