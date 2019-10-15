<?php

namespace SomethingDigital\CustomerSpecificPricing\Controller\Prices;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Index extends Action
{
    /**
     * @var SpotPricingApi
     */
    private $spotPricingApi;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    protected $productRepository;

    public function __construct(
        Context $context,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
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

                if ($spotPrice < $product->getFinalPrice()) {
                    $data[$sku] = $spotPrice;
                }
                if ($product->getExactUnitPrice() && $spotPrice != 0) {
                    $data[$sku] = $spotPrice * 100;
                } elseif ($product->getExactUnitPrice() && $spotPrice == 0) {
                    $data[$sku] = $product->getExactUnitPrice() * 100;
                }

                $data[$sku] = [
                    'price' => round($data[$sku], 2),
                    'QtyPrice1' => $this->arrayManager->get('body/QtyPrice1', $prices, 0),
                    'QtyPrice2' => $this->arrayManager->get('body/QtyPrice2', $prices, 0),
                    'QtyPrice3' => $this->arrayManager->get('body/QtyPrice3', $prices, 0),
                    'QtyBreak1' => $this->arrayManager->get('body/QtyBreak1', $prices, 0),
                    'QtyBreak2' => $this->arrayManager->get('body/QtyBreak2', $prices, 0),
                    'QtyBreak3' => $this->arrayManager->get('body/QtyBreak3', $prices, 0)
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
