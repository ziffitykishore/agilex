<?php

namespace SomethingDigital\OrderHistory\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use SomethingDigital\Sx\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class OrdersApi extends Adapter
{
    protected $session;

    public function __construct(
        ClientFactory $curlFactory,
        Logger $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor
    ) {
        parent::__construct(
            $curlFactory,
            $logger,
            $config,
            $storeManager,
            $testMode,
            $configWriter,
            $cacheTypeList,
            $encryptor
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
            $this->requestPath = 'api/Order?customerId=' . $customerAccountId;

            if (!empty($params)) {
                $this->requestPath .= '&' . http_build_query($params);
            }

            return $this->getRequest();
        } else {
            return [];
        }
    }

    /**
     * @param int $orderId
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
