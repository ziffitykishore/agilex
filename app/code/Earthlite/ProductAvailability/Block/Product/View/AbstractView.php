<?php

namespace Earthlite\ProductAvailability\Block\Product\View;

class AbstractView extends \Magento\Framework\View\Element\Template
{
    protected $_stockItemRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        $this->_stockItemRepository = $stockItemRepository;
        parent::_construct($context, $data);
    }

    public function getStockItem($productId)
    {
        return $this->_stockItemRepository->get($productId);
    }
}
