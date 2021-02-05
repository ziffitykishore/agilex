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
                $result[$sku]['qty'] = ($this->stockData->getQtyIncrement($sku) > 0) ? $this->stockData->getQtyIncrement($sku) : $result[$sku]['qty'];
                $result[$sku]['stockData'] = $stockData;
                $result[$sku]['stockInfo'] = $this->stockData->getMinSaleQtyAndIncrementsInfo($sku);
            }
        }
        return $result;
    }
}
