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

class Index extends Action
{
    private $spotPricingApi;
    private $arrayManager;
    private $currency;
    protected $logger;
    protected $productRepository;

    public function __construct(
        Context $context,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $currency
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->currency = $currency;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParam('products');
        $jsonResult = $this->resultFactory->create('json');

        $data = [];

        try {
            foreach ($params as $id => $sku) {
                $prices = $this->spotPricingApi->getSpotPrice($sku);
                $spotPrice = $this->arrayManager->get('body/DiscountPrice', $prices, 0);
                $product = $this->productRepository->get($sku);
                $regularPrice = $product->getFinalPrice();

                if ($spotPrice < $regularPrice) {
                    $price = $spotPrice;
                } else {
                    $price = $regularPrice;
                }
                $unitPrice = $price;
                if ($product->getExactUnitPrice()) {
                    $price = $price * 100;
                }

                $qtyPrice1 = round($this->arrayManager->get('body/QtyPrice1', $prices), 2);
                $qtyPrice2 = round($this->arrayManager->get('body/QtyPrice2', $prices), 2);
                $qtyPrice3 = round($this->arrayManager->get('body/QtyPrice3', $prices), 2);
                $qtyBreak1 = round($this->arrayManager->get('body/QtyBreak1', $prices), 2);
                $qtyBreak2 = round($this->arrayManager->get('body/QtyBreak2', $prices), 2);
                $qtyBreak3 = round($this->arrayManager->get('body/QtyBreak3', $prices), 2);

                $data[$sku] = [
                    'price' => round($price, 2),
                    'unitPrice' => $unitPrice,
                    'QtyPrice1' => $qtyPrice1 ? $qtyPrice1 : '',
                    'QtyPrice2' => $qtyPrice2 ? $qtyPrice2 : '',
                    'QtyPrice3' => $qtyPrice3 ? $qtyPrice3 : '',
                    'QtyBreak1' => $qtyBreak1 ? $qtyBreak1 : '',
                    'QtyBreak2' => $qtyBreak2 ? $qtyBreak2 : '',
                    'QtyBreak3' => $qtyBreak3 ? $qtyBreak3 : '',
                    'currencySymbol' => $this->currency->getCurrency()->getCurrencySymbol()
                ];
            }
        } catch (LocalizedException $e) {
            $this->logger->critical('Request has failed with exception: ' . $e->getMessage());
            return $this->prepareFailedJsonResult($e->getMessage(), $jsonResult);
        }

        $jsonResult->setHttpResponseCode(200);
        $jsonResult->setData(
            [
                'status' => 'success',
                'code' => 200,
                'data' => $data
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
