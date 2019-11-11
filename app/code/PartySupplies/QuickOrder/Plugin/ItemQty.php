<?php

namespace PartySupplies\QuickOrder\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class ItemQty
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    
    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * 
     * @param ProductRepository $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry,
        JsonHelper $jsonHelper
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->_jsonHelper = $jsonHelper;
    }

    /**
     * To add MinSaleQty
     *
     * @param \Mageplaza\QuickOrder\Controller\Items\Itemqty $subject
     * @param json $result
     * @return json
     */
    public function afterExecute(
        \Mageplaza\QuickOrder\Controller\Items\Itemqty $subject,
        $result
    ) {
        $product = $this->productRepository->get($subject->getRequest()->getParam('itemsku'));

        $stockItem = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );

        $result = json_decode($result->getBody(), true);
        
        $result['minSaleQty'] = $stockItem->getMinSaleQty();

        return $subject->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
    }
}
