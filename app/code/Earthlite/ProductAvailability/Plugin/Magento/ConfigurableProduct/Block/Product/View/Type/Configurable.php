<?php

namespace Earthlite\ProductAvailability\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

use Magento\CatalogInventory\Api\StockStateInterfaceFactory;

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
     * 
     * @param StockStateInterfaceFactory $stockStateInterface
     */
    public function __construct(
        StockStateInterfaceFactory $stockStateInterface
    ) {
        $this->stockStateInterface = $stockStateInterface;
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
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}
