<?php

namespace PartySupplies\QuickOrder\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class PreItem
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var $stockRegistry
     */
    protected $stockRegistry;

    /**
     * 
     * @param ProductRepository $productRepository
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
    }
    
    /**
     * To modify parameter
     * 
     * @param \Mageplaza\QuickOrder\Controller\Items\Preitem $subject
     * @return null
     */
    public function beforeExecute(
        \Mageplaza\QuickOrder\Controller\Items\Preitem $subject
    ) {
        
        $params = $subject->getRequest()->getParam('value');

        $modifiedProductData = [];
        foreach ($params as $param)
        {
            $productData = explode(',', $param);

            if (isset($productData[1])) {
                $product = $this->productRepository->get($productData[0]);

                $stockItem = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
                $minSaleQty = $stockItem->getMinSaleQty();

                if ($productData[1] < $minSaleQty) {
                    // Set MinSaleQty
                    $productData[1] = $minSaleQty;

                } elseif ($productData[1] > $minSaleQty && !$productData[1] % $minSaleQty == 0) {
                    // Set RoundOff Qty
                    $roundOffQty = round($productData[1] / $minSaleQty) * $minSaleQty;
                    $productData[1] = $roundOffQty;

                }
            } else {
                $product = $this->productRepository->get($productData[0]);

                $stockItem = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
                //Set MinSaleQty
                $productData[1] = $stockItem->getMinSaleQty();
            }
            array_push($modifiedProductData, implode(',', $productData));
        }

        $subject->getRequest()->setParam('value', $modifiedProductData);

        return null;
    }
}
