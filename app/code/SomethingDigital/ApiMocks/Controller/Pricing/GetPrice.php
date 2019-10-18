<?php

namespace SomethingDigital\ApiMocks\Controller\Pricing;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
 
class GetPrice extends \Magento\Framework\App\Action\Action
{
    /** @var JsonFactory */
    protected $jsonFactory;

    /**
     * AbstractController constructor.
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    public function execute() 
    {
        $resultJson = $this->jsonFactory->create();
        $params = $this->getRequest()->getParams();

        $failure = $this->checkDataInvalid($params);
        if (!empty($failure)) {
            $resultJson->setData(['error' => $failure]);
        } else {
            $resultJson->setData($this->getResult($params));
        }
        return $resultJson;
    }

    protected function checkDataInvalid($params) {
    	$errors = [];
        if (empty($params['sku'])) {
            $errors['sku'] = 'Sku is empty';
        }
    	return $errors;
    }

    protected function getResult($params)
    {
        $result = [
            "DiscountAmount" => 10,
            "DiscountType" => "%",
            "ErrorMessage" => "",
            "ExtendedAmount" => 23.99,
            "ExtendedDiscountAmount" => 2.666,
            "NetAvailable" => 2,
            "DiscountPrice" => 26.66,
            "PriceCostPer" => "",
            "PriceOriginCode" => "4",
            "PricingRecordNumber" => 6379745,
            "PromotionalFlag" => false,
            "SpecialConversion" => 1,
            "SpecialCostRecordNumber" => 1,
            "SpecialCostType" => "",
            "StockingQuantityOrdered" => 1,
            "UnitConversion" => 1,
            "UnitsPerStocking" => 1,
            "UnitsPerStockingText" => "",
            "QtyPrice1" => 19.9456,
            "QtyPrice2" => "",
            "QtyPrice3" => "",
            "QtyBreak1" => 12,
            "QtyBreak2" => "",
            "QtyBreak3" => ""
        ];

        if (isset($params['suffix'])) {
            if ($params['suffix'] == 'SUF1') {
                $result['DiscountPrice'] = 19.99;
            } elseif ($params['suffix'] == 'SUF2') {
                $result['DiscountPrice'] = 21;
            }
        }

        return $result;
    }
}