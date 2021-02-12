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
use Magento\Framework\App\Request\Http;

class SpotPricingApi extends Adapter
{

    protected $path = 'api/Pricing';
    protected $session;
    protected $sessionManager;
    protected $cart;
    protected $currency;
    protected $arrayManager;
    protected $request;

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
        ArrayManager $arrayManager,
        Http $request
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
        $this->request = $request;
    }

    /**
     * @param array $productSku
     * @return array
     * @throws LocalizedException
     */
    public function getSpotPrice($productSkus, $suffix = null)
    {
        $customerAccountId = $this->getCustomerAccountId();
        $skuCollection = [];
        $skuCollection_item = [];
        if ($this->request->getControllerName() != 'result') {
            $items = $this->cart->getQuote()->getAllVisibleItems();
            $suffixArr = [];
            foreach ($items as $quoteItem) {
                if ($productSkus && is_array($productSkus)) {
                    $suffixArrstep = [];
                    foreach ($productSkus as $sku) {
                        if($quoteItem->getSku() == $sku) {
                            if (!empty($quoteItem->getData('suffix'))) {
                                $suffixArrstep['Suffix'] = $quoteItem->getData('suffix');
                            }
                            $suffixArrstep['Sku'] = $quoteItem->getSku();
                        }
                    }
                    if (!empty($suffixArrstep)) {
                        $suffixArr[] = $suffixArrstep;
                    }
                }
            }
            
            $skuCollection_item = (array_values($suffixArr));
        }
        $skuCollection = $skuCollection_item;
        
        if ($customerAccountId === 0 && empty($skuCollection)) {
            return false;
        }


        $response = $this->getSpotPriceResponse($skuCollection, $customerAccountId);
        
        return $response;
    }

    /**
     * @param array $productSku
     * @return array
     * @throws LocalizedException
     */
    public function getSpotPriceDPD($productSkus, $suffix = null)
    {
        $customerAccountId = $this->getCustomerAccountId();
        $skuCollectionSuffix = [];
        $skuCollection = [];
        if (!$suffix) {
            $suffix = $this->sessionManager->getSkuSuffix();
            $suffixArr = [];
            if (!empty($suffix)) {
                $suffixDecode = json_decode($suffix);
                if ($productSkus && is_array($productSkus)) {
                    foreach ($productSkus as $sku) {
                        if ($suffixDecode && is_array($suffixDecode)) {
                            foreach ($suffixDecode as $suffixValue) {
                                $suffixCode = explode('~', $suffixValue);
                                $suffixArr['Suffix'] = $suffixCode[0];
                                $suffixArr['Sku'] = $suffixCode[1];
                            }
                        }
                    }
                }
            }            
            $skuCollection = [(object)$suffixArr];
        }
        
        if ($customerAccountId === 0 && empty($skuCollection)) {
            return false;
        }

        $response = $this->getSpotPriceResponse($skuCollection, $customerAccountId);
        
        return $response;
    }

    /**
     * @param array $productSku
     * @return array
     * @throws LocalizedException
     */
    private function getSpotPriceResponse($skuCollection, $customerAccountId = null)
    {
        if (!empty($skuCollection)) {
            if (!$this->isTestMode()) {
                $suffix = $this->cart->getQuote()->getSuffix();
                $suffix = $suffix ? $suffix : '';
                if (!empty($customerAccountId) && $customerAccountId !== 0 && empty($suffix)) {
                    $this->requestBody = [
                        'TraversAccountId' => $customerAccountId,
                        'Skus' => $skuCollection
                    ];
                } elseif (!empty($suffix) && !empty($customerAccountId) && $customerAccountId !== 0) {
                    $this->requestBody = [
                        'TraversAccountId' => $customerAccountId,
                        'CartSuffix' => $suffix,
                        'Skus' => $skuCollection
                    ];
                } elseif (!empty($suffix)) {
                    $this->requestBody = [
                        'CartSuffix' => $suffix,
                        'Skus' => $skuCollection
                    ];
                } else {
                    $this->requestBody = [
                        'Skus' => $skuCollection
                    ];
                }
                
                $this->requestPath = $this->path.'/';
            } else {
                $this->requestPath = 'api-mocks/Pricing/GetPrice?'. http_build_query([
                    'customerId' => $customerAccountId,
                    'sku' => $productSku,
                    'suffix' => $suffix
                ]);
            }
            $response = $this->postRequest();
    
            if ($response && isset($response['body']) && $this->isSuccessful($response['status'])) {
                return $this->convertPrices($response['body']);
            }
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
