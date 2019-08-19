<?php

namespace SomethingDigital\CustomerBalance\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;

class CustomerBalanceApi extends Adapter
{
    protected $session;

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode
    ) {
        parent::__construct(
            $curlFactory,
            $logger,
            $config,
            $storeManager,
            $testMode
        );
        $this->session = $session;
    }

    /**
     * @param array $params
     * @return array
     * @throws LocalizedException
     */
    public function getCustomerBalance($params = [])
    {
        $customerAccountId = $this->getCustomerAccountId();

        if ($customerAccountId) {
            $this->requestPath = 'api/Customer/' . $customerAccountId . '/' . 'Balance';

            if (!empty($params)) {
                $this->requestPath .= '?' . http_build_query($params);
            }

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
