<?php

namespace Earthlite\ProductAvailability\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

use Magento\CatalogInventory\Api\StockStateInterfaceFactory;
use Earthlite\EstimatedShipping\Helper\EstimateShipping;
/**
 * Configurable class
 */
class Configurable
{
    /**
     *
     * @var StockStateInterfaceFactory 
     */
    protected $stockStateInterface;

    /**
     * @var EstimateShipping
     */
    protected $estimateShipping;

    /**
     * 
     * @param StockStateInterfaceFactory $stockStateInterface
     */
    public function __construct(
        StockStateInterfaceFactory $stockStateInterface,
        EstimateShipping $estimateShipping
    ) {
        $this->stockStateInterface = $stockStateInterface;
        $this->estimateShipping = $estimateShipping;
    }

    /**
     * afterGetJsonConfig function
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param null $result
     * @return void
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $result
    ) {
        $jsonResult = json_decode($result, true);
        $jsonResult['simpleQtys'] = [];
        foreach ($subject->getAllowProducts() as $product) {
            $productId = $product->getId();
            /** @var \Magento\CatalogInventory\Api\StockStateInterface $stockState **/
            $stockState = $this->stockStateInterface->create();
            $jsonResult['simpleQtys'][$productId] = $stockState->getStockQty($productId);
            if ($product->getData('production_item')) {
                $jsonResult['leadTime'][$productId] = $this->estimateShipping->getShippingInfo($product->getSku());
            }
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}
