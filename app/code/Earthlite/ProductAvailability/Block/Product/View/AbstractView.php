<?php
namespace Earthlite\ProductAvailability\Block\Product\View;

use Magento\CatalogInventory\Api\StockStateInterfaceFactory;
use Magento\Backend\Block\Template\Context;

/**
 * class AbstractView
 */
class AbstractView extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var StockStateInterfaceFactory
     */
    protected $_stockItemRepository;

    /**
     * AbstractView constructor
     * 
     * @param Context $context
     * @param StockStateInterfaceFactory $stockStateInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockStateInterfaceFactory $stockStateInterface,
        array $data = []
    ) {
        $this->stockStateInterface = $stockStateInterface;
        parent::_construct($context, $data);
    }

    /**
     * 
     * @param int $productId
     * @return float
     */
    public function getStockQty($productId)
    {
        /** @var \Magento\CatalogInventory\Api\StockStateInterface $stockState **/
        $stockState = $this->stockStateInterface->create();
        return $stockState->getStockQty($productId);
    }
}
