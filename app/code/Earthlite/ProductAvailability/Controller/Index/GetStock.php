<?php
declare(strict_types = 1);
namespace Earthlite\ProductAvailability\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\CatalogInventory\Api\StockStateInterfaceFactory;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * class GetStock
 */
class GetStock extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var StockStateInterfaceFactory 
     */
    protected $stockStateInterface;
    
    /**
     * 
     * @param Context $context
     * @param StockStateInterfaceFactory $stockStateInterface
     * @param JsonFactory $resultJsonFactory
     * @return type
     */
    public function __construct(
        Context $context,
        StockStateInterfaceFactory $stockStateInterface,
        JsonFactory $resultJsonFactory
    ) {
        $this->stockStateInterface = $stockStateInterface;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }
    
    /**
     * 
     * @return ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('id');
        /** @var \Magento\CatalogInventory\Api\StockStateInterface $stockState * */
        $stockState = $this->stockStateInterface->create();
        $productQty = $stockState->getStockQty($productId);
        $result = $this->resultJsonFactory->create();
        $result->setData(['qty' => $productQty]);
        return $result;
    }

}
