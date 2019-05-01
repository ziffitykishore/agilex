<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

class SpotPricingApi extends Adapter
{

    protected $path = 'api/Pricing';
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
     * @param string $productSku
     * @return array
     * @throws LocalizedException
     */
    public function getSpotPrice($productSku)
    {
        $this->requestPath = $this->path.'/'.rawurlencode($productSku).'?' . http_build_query([
            'customerId' => $this->getCustomerAccountId()
        ]);

        return $this->getRequest();
    }

    /**
     * @return string
     * @throws ApiRequestException
     */
    protected function getCustomerAccountId()
    {
        if (($accountId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_account_id'))) {
            return $accountId->getValue();
        }

        throw new ApiRequestException(__('Request requires Customer account ID.'));
    }

}
