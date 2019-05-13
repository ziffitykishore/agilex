<?php

namespace SomethingDigital\OrderHistory\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

class OrdersApi extends Adapter
{
    protected $session;

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session
    ) {
        parent::__construct(
            $curlFactory,
            $logger,
            $config,
            $storeManager
        );
        $this->session = $session;
    }

    /**
     * @param array $params
     * @return array
     * @throws LocalizedException
     */
    public function getOrders($params = [])
    {
        $customerAccountId = $this->getCustomerAccountId();

        if ($customerAccountId) {
            $this->requestPath = 'api/Customer/' . $customerAccountId . '/' . 'Orders';

            if (!empty($params)) {
                $this->requestPath .= '?' . http_build_query($params);
            }

            return $this->getRequest();
        } else {
            return [];
        }
    }

    /**
     * @param array $orderId
     * @return array
     * @throws LocalizedException
     */
    public function getOrder($orderId)
    {
        $customerAccountId = $this->getCustomerAccountId();

        if ($customerAccountId) {
            $this->requestPath = 'api/Order/' . $orderId;

            return $this->getRequest();
        } else {
            return [];
        }
    }

    /**
     * @return string
     */
    protected function getCustomerAccountId()
    {
        if (($accountId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_account_id'))) {
            return $accountId->getValue();
        } else {
            return false;
        }
    }

}
