<?php

namespace SomethingDigital\StockInfo\Plugin;

use Magento\QuickOrder\Model\Cart;
use SomethingDigital\StockInfo\Model\StockData;

class UpdateAjaxResponse
{
    /**
     * @var StockData
     */
    private $stockData;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        StockData $stockData
    ) {
        $this->stockData = $stockData;
    }

    public function afterGetAffectedItems(Cart $subject, $result)
    {
        if (isset($result)) {
            foreach ($result as $item) {
                $sku = $item['sku'];
                $stockData = $this->stockData->getStockData($sku);
                $minSaleQtyAndIncrements = $this->stockData->getMinSaleQtyAndIncrements($sku);
                $result[$sku]['stockData'] = $stockData;
                $result[$sku]['min_sale_qty'] = $minSaleQtyAndIncrements['min_sale_qty'];
                $result[$sku]['qty_increments'] = $minSaleQtyAndIncrements['qty_increments'];
            }
        }
        return $result;
    }
}
