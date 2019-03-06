<?php

namespace SomethingDigital\StockInfo\ViewModel;

class StockData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Stock data model
     *
     * @var \SomethingDigital\StockInfo\Model\StockData
     */
    private $stockData;

    /**
     * @param \SomethingDigital\StockInfo\Model\StockData $stockData
     */
    public function __construct(
        \SomethingDigital\StockInfo\Model\StockData $stockData
    ) {
        $this->stockData = $stockData;
    }

    /**
     * Get stock data by warehouses for current product
     *
     * @return []
     */
    public function getStockData()
    {
        return $this->stockData->getStockData();
    }

    /**
     * Get current product type
     *
     * @return string
     */
    public function getProductType()
    {
        return $this->stockData->getProductType();
    }
}
