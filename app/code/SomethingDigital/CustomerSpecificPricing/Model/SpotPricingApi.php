<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class SpotPricingApi extends Adapter
{

    protected $path = 'api/Pricing';
    protected $session;
    protected $sessionManager;
    protected $cart;

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode,
        SessionManagerInterface $sessionManager,
        Cart $cart,
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
        $this->sessionManager = $sessionManager;
        $this->cart = $cart;
    }

    /**
     * @param string $productSku
     * @return array
     * @throws LocalizedException
     */
    public function getSpotPrice($productSku)
    {
        $customerAccountId = $this->getCustomerAccountId();

        $suffix = $this->sessionManager->getSkuSuffix();
        if (empty($suffix)) {
            $suffix = $this->cart->getQuote()->getSuffix();
        }

        if (!$this->isTestMode()) {
            $this->requestPath = $this->path.'/'.rawurlencode($productSku).'?' . http_build_query([
                'customerId' => $customerAccountId,
                'suffix' => $suffix
            ]);
        } else {
            $this->requestPath = 'api-mocks/Pricing/GetPrice?'. http_build_query([
                'customerId' => $customerAccountId,
                'sku' => $productSku,
                'suffix' => $suffix
            ]);
        }

        return $this->getRequest();
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
