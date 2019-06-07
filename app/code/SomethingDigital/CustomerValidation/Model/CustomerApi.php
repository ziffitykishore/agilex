<?php

namespace SomethingDigital\CustomerValidation\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;

class CustomerApi extends Adapter
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
     * @param int $orderId
     * @return array
     * @throws LocalizedException
     */
    public function getCustomer($customerAccountId)
    {
        if ($customerAccountId) {
            $this->requestPath = 'api/Customer/' . $customerAccountId;

            return $this->getRequest();
        } else {
            return [];
        }
    }

}
