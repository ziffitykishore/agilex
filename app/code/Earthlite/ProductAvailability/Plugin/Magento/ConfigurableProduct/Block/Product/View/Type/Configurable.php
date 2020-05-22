<?php

namespace Earthlite\ProductAvailability\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

/**
 * Configurable class
 */
class Configurable
{
    private $stockItemRepository;

    /**
     * Construct function
     *
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
        $this->stockItemRepository = $stockItemRepository;
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
            $productQty = $this->stockItemRepository->get($productId);
            $jsonResult['simpleQtys'][$productId] = $productQty->getQty();
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}
