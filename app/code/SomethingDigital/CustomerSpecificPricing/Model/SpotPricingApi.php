<?php

namespace SomethingDigital\CustomerSpecificPricing\Model;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use SomethingDigital\Sx\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayManager;

class SpotPricingApi extends Adapter
{

    protected $path = 'api/Pricing';
    protected $session;
    protected $sessionManager;
    protected $cart;
    protected $currency;
    protected $arrayManager;

    public function __construct(
        ClientFactory $curlFactory,
        Logger $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        Session $session,
        TestMode $testMode,
        SessionManagerInterface $sessionManager,
        Cart $cart,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor,
        PriceCurrencyInterface $currency,
        ArrayManager $arrayManager
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
        $this->currency = $currency;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param array $productSku
     * @return array
     * @throws LocalizedException
     */
    public function getSpotPrice($productSkus, $suffix = null)
    {
        $customerAccountId = $this->getCustomerAccountId();

        if (!$suffix) {
            $suffix = $this->sessionManager->getSkuSuffix();
        }

        if (empty($suffix)) {
            $suffix = $this->cart->getQuote()->getSuffix();
        }

        if ($customerAccountId === 0 && empty($suffix)) {
            return false;
        }

        if (!$this->isTestMode()) {
            $this->requestPath = $this->path.'/?' . http_build_query([
                'customerId' => $customerAccountId,
                'brochurePrefix' => $suffix
            ]);
        } else {
            $this->requestPath = 'api-mocks/Pricing/GetPrice?'. http_build_query([
                'customerId' => $customerAccountId,
                'sku' => $productSku,
                'suffix' => $suffix
            ]);
        }

        if ($productSkus && is_array($productSkus)) {
            foreach ($productSkus as $sku) {
                $this->requestBody[] = rawurlencode($sku);
            }
        }

        $response = $this->postRequest();

        if ($response && isset($response['body']) && $this->isSuccessful($response['status'])) {
            return $this->convertPrices($response['body']);
        }
        return false;
    }

    /**
     * @return string
     */
    protected function getCustomerAccountId()
    {
        if (($accountId = $this->session->getCustomerDataObject()->getCustomAttribute('travers_account_id'))) {
            return $accountId->getValue();
        } else {
            return 0;
        }
    }

    /**
     * @return array
     */
    protected function convertPrices($response)
    {
        $store = $this->storeManager->getStore()->getStoreId();

        if (is_array($response)) {
            foreach ($response as $key => $productPrices) {
                $response[$key]['DiscountPrice'] = $this->currency->convert(
                    $this->arrayManager->get('DiscountPrice', $productPrices, 0),
                    $store
                );
                $response[$key]['QtyPrice1'] = $this->currency->convert(
                    $this->arrayManager->get('QtyPrice1', $productPrices, 0),
                    $store
                );
                $response[$key]['QtyPrice2'] = $this->currency->convert(
                    $this->arrayManager->get('QtyPrice2', $productPrices, 0),
                    $store
                );
                $response[$key]['QtyPrice3'] = $this->currency->convert(
                    $this->arrayManager->get('QtyPrice3', $productPrices, 0),
                    $store
                );
            }
        }

        return $response;
    }
}
