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
        $type = $this->getRequest()->getParam('type');
        $jsonResult = $this->resultFactory->create('json');

        if (!$skus) {
            return $jsonResult;
        }

        $data = [];
        $canShowSuffix = false;

        try {
            $prices = $this->spotPricingApi->getSpotPriceDPD($skus);
            $store = $this->storeManager->getStore()->getStoreId();
            
            if ($prices) {
                foreach ($prices as $id => $productPrices) {
                    $spotPrice = $this->arrayManager->get('DiscountPrice', $productPrices, 0);
                    $sku = $this->arrayManager->get('Sku', $productPrices);
                    $product = $this->productRepository->get($sku);
                    $price = $this->currency->convert($product->getFinalPrice(), $store);
                    $pricePer100 = false;
                    if (!empty($spotPrice) && $spotPrice < $price) {
                        $price = $spotPrice;
                    }
                    $unitPrice = $price;
                    if (!empty($product->getExactUnitPrice()) && $product->getExactUnitPrice() > 0) {
                        $unitPrice = min($product->getExactUnitPrice(), $price);
                        $price = $this->currency->convert($unitPrice, $store) * 100;
                        $pricePer100 = true;
                    }
                    if (!empty($product->getSpecialExactUnitPrice()) && $product->getSpecialExactUnitPrice() > 0) {
                        $exactUnitPrice = min($product->getSpecialExactUnitPrice(), $price);
                        $price = $this->currency->convert($exactUnitPrice, $store) * 100;
                    }

                    $qtyPrice1 = $this->arrayManager->get('QtyPrice1', $productPrices);
                    $qtyPrice2 = $this->arrayManager->get('QtyPrice2', $productPrices);
                    $qtyPrice3 = $this->arrayManager->get('QtyPrice3', $productPrices);
                    $qtyBreak1 = round($this->arrayManager->get('QtyBreak1', $productPrices));
                    $qtyBreak2 = round($this->arrayManager->get('QtyBreak2', $productPrices));
                    $qtyBreak3 = round($this->arrayManager->get('QtyBreak3', $productPrices));

                    if ($this->arrayManager->get('BrochurePricing', $productPrices) !== null &&
                        (
                            $this->arrayManager->get('BrochurePricing/DiscountPrice', $productPrices) !== null ||
                            $this->arrayManager->get('BrochurePricing/QtyPrice1', $productPrices) !== null ||
                            $this->arrayManager->get('BrochurePricing/QtyPrice2', $productPrices) !== null ||
                            $this->arrayManager->get('BrochurePricing/QtyPrice3', $productPrices) !== null
                        )
                    ) {
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
                        'pricePer100' => $pricePer100,
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
            } else {
                if ($type == 'false') {
                    foreach ($skus as $sku) {
                        $product = $this->productRepository->get($sku);

                        if ($product->getExactUnitPrice()) {
                            $unitPrice = $product->getExactUnitPrice();
                            $specialUnitPrice = $product->getSpecialExactUnitPrice();
                            $price = $this->currency->convert($unitPrice, $store) * 100;

                            if ($specialUnitPrice) {
                                $price = $this->currency->convert($specialUnitPrice, $store) * 100;
                            }

                            $data[$sku] = [
                                'price' => number_format($price, 2),
                                'unitPrice' => $unitPrice
                            ];
                        }
                    }
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
