<?php

namespace SomethingDigital\CustomerSpecificPricing\Controller\Prices;

use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Index extends Action
{
    /**
     * @var SpotPricingApi
     */
    private $spotPricingApi;

    public function __construct(
        Context $context,
        SpotPricingApi $spotPricingApi
    ) {
        $this->spotPricingApi = $spotPricingApi;
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
                $data[$sku] = $prices['body']['Price'];
            }
        } catch (LocalizedException $e) {
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
