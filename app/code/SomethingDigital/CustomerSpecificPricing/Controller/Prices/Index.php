<?php

namespace SomethingDigital\CustomerSpecificPricing\Controller\Prices;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    private $spotPricingApi;
    private $arrayManager;
    private $currency;
    protected $logger;
    protected $productRepository;
    protected $sessionManager;
    protected $storeManager;

    public function __construct(
        Context $context,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $currency,
        SessionManagerInterface $sessionManager,
        StoreManagerInterface $storeManager
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->currency = $currency;
        $this->sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $skus = $this->getRequest()->getParam('products');
        $jsonResult = $this->resultFactory->create('json');

        if (!$skus) {
            return $this->prepareFailedJsonResult('Empty skus.', $jsonResult);
        }

        $data = [];
        $canShowSuffix = false;

        try {
            $prices = $this->spotPricingApi->getSpotPrice($skus);

            if ($prices) {
                foreach ($prices as $id => $productPrices) {
                    $store = $this->storeManager->getStore()->getStoreId();
                    $spotPrice = $this->currency->convert(
                        $this->arrayManager->get('DiscountPrice', $productPrices, 0),
                        $store
                    );
                    $sku = $this->arrayManager->get('Sku', $productPrices);
                    $product = $this->productRepository->get($sku);
                    $price = $this->currency->convert($product->getFinalPrice(), $store);

                    if (!empty($spotPrice) && $spotPrice < $price) {
                        $price = $spotPrice;
                        $canShowSuffix = true;
                    }
                    $unitPrice = $price;
                    if ($product->getExactUnitPrice()) {
                        $price = $this->currency->convert($product->getExactUnitPrice(), $store) * 100;
                    }

                    $qtyPrice1 = $this->currency->convert($this->arrayManager->get('QtyPrice1', $productPrices), $store);
                    $qtyPrice2 = $this->currency->convert($this->arrayManager->get('QtyPrice2', $productPrices), $store);
                    $qtyPrice3 = $this->currency->convert($this->arrayManager->get('QtyPrice3', $productPrices), $store);
                    $qtyBreak1 = round($this->arrayManager->get('QtyBreak1', $productPrices));
                    $qtyBreak2 = round($this->arrayManager->get('QtyBreak2', $productPrices));
                    $qtyBreak3 = round($this->arrayManager->get('QtyBreak3', $productPrices));

                    if ($qtyPrice1 || $qtyPrice2 || $qtyPrice3) {
                        $canShowSuffix = true;
                    }

                    $msrp = $product->getCustomAttribute('manufacturer_price');

                    if ($msrp && $msrp->getValue()) {
                        $msrpVal = $msrp->getValue();
                    } else {
                        $msrpVal = '';
                    }

                    $data[$sku] = [
                        'price' => number_format($price, 2),
                        'unitPrice' => $unitPrice,
                        'msrp' => $msrpVal,
                        'QtyPrice1' => $qtyPrice1 ? number_format($qtyPrice1, 2) : '',
                        'QtyPrice2' => $qtyPrice2 ? number_format($qtyPrice2, 2) : '',
                        'QtyPrice3' => $qtyPrice3 ? number_format($qtyPrice3, 2) : '',
                        'QtyBreak1' => $qtyBreak1 ? $qtyBreak1 : '',
                        'QtyBreak2' => $qtyBreak2 ? $qtyBreak2 : '',
                        'QtyBreak3' => $qtyBreak3 ? $qtyBreak3 : '',
                        'currencySymbol' => $this->currency->getCurrency()->getCurrencySymbol()
                    ];
                }
            }
        } catch (LocalizedException $e) {
            $this->logger->critical('Request has failed with exception: ' . $e->getMessage());
            return $this->prepareFailedJsonResult($e->getMessage(), $jsonResult);
        }

        $suffix = $this->sessionManager->getSkuSuffix();
        if ($canShowSuffix) {
            $suffix = $this->sessionManager->getSkuSuffix();
        } else {
            $suffix = '';
        }

        $jsonResult->setHttpResponseCode(200);
        $jsonResult->setData(
            [
                'status' => 'success',
                'code' => 200,
                'data' => $data,
                'suffix' => $suffix
            ]
        );
        return $jsonResult;
    }

    private function prepareFailedJsonResult(string $reason, $jsonResult)
    {
        $jsonResult->setHttpResponseCode(400);
        $jsonResult->setData(
            [
                'status' => 'error',
                'code' => 400,
                'reason' => $reason
            ]
        );
        return $jsonResult;
    }
}
