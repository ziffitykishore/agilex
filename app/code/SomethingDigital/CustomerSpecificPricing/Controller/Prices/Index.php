<?php

namespace SomethingDigital\CustomerSpecificPricing\Controller\Prices;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        Context $context,
        SpotPricingApi $spotPricingApi,
        ArrayManager $arrayManager,
        LoggerInterface $logger
    ) {
        $this->spotPricingApi = $spotPricingApi;
        $this->arrayManager = $arrayManager;
        $this->logger = $logger;
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
                $data[$sku] = $this->arrayManager->get('body/Price', $prices);
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
