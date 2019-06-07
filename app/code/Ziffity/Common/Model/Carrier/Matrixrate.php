<?php

namespace Ziffity\Common\Model\Carrier;

use WebShopApps\MatrixRate\Model\Carrier\Matrixrate as DefaultMatrixRate;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Matrixrate extends DefaultMatrixRate
{
    
    /**
     * @var string
     */
    protected $_code = 'matrixrate';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var string
     */
    protected $defaultConditionName = 'package_weight';

    /**
     * @var array
     */
    protected $conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $resultMethodFactory;

    /**
     * @var \WebShopApps\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory
     */
    protected $matrixrateFactory;    
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        \WebShopApps\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory $matrixrateFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $rateResultFactory, $resultMethodFactory ,$matrixrateFactory, $data);
    }
    
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if (!$request->getConditionMRName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionMRName($conditionName ? $conditionName : $this->defaultConditionName);
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        
        /* Custom logic to calculate no of balloons */

        $balloonCount = 0;
        $visitedSku = [];
        foreach ($request->getAllItems() as $item) {
            if ($item->getProduct()->getTypeId() === 'bundle') {
                $childCount = 0;
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        if($child->getProduct()->getTypeId() === 'simple'){
                            $childCount += $child->getQty();
                            array_push($visitedSku, $child->getSku());
                        }
                    }
                    $balloonCount += $childCount;
                }
                $balloonCount *= $item->getQty();
            }else if($item->getProduct()->getTypeId() === 'simple'){
                $index = array_search($item->getSku(), $visitedSku);
                if($index === false){
                    $balloonCount +=$item->getQty();
                }else{
                    unset($visitedSku[$index]);
                }
            }else if($item->getProduct()->getTypeId() === 'configurable'){
                if ($item->getHasChildren()) {
                    $balloonCount +=$item->getQty();
                    array_push($visitedSku, $item->getSku());
                }

            }
        }

        if($balloonCount){
            $request->setPackageQty((float)$balloonCount);
        }

        /* Custom logic to calculate no of balloons */        
        
        
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        $zipRange = $this->getConfigData('zip_range');
        $rateArray = $this->getRate($request, $zipRange);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        $foundRates = false;

        foreach ($rateArray as $rate) {
            if (!empty($rate) && $rate['price'] >= 0) {
                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->resultMethodFactory->create();

                $method->setCarrier('matrixrate');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('matrixrate_' . $rate['pk']);
                $method->setMethodTitle(__($rate['shipping_method']));

                if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                    $shippingPrice = 0;
                } else {
                    $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                }

                $method->setPrice($shippingPrice);
                $method->setCost($rate['cost']);

                $result->append($method);
                $foundRates = true; // have found some valid rates
            }
        }

        if (!$foundRates) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }    
}
