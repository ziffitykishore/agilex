<?php
declare(strict_types = 1);
namespace Earthlite\Checkout\Plugin\Model\IsProductSalableForRequestedQtyCondition;

use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\IsAnySourceItemInStockCondition;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory;

/**
 * Class IsAnySourceItemInStockConditionPlugin
 */
class IsAnySourceItemInStockConditionPlugin
{
    /**
     * IsAnySourceItemInStockConditionPlugin constructor 
     * 
     * @param ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory
     * @param ProductSalableResultInterfaceFactory $productSalableResultFactory
     */
    public function __construct(
       ProductRepositoryInterfaceFactory $productRepositoryInterfaceFactory,
       ProductSalableResultInterfaceFactory $productSalableResultFactory
    ) {
        $this->productSalableResultFactory = $productSalableResultFactory;
        $this->productRepositoryInterfaceFactory = $productRepositoryInterfaceFactory;
    }

    /**
     * 
     * @param IsAnySourceItemInStockCondition $subject
     * @param callable $proceed
     * @param string $sku
     * @param int $stockId
     * @param float $requestedQty
     * @param ProductSalableResultInterface $result
     */
    public function aroundExecute(
        IsAnySourceItemInStockCondition $subject, 
        callable $proceed,
        string $sku,
        int $stockId,
        float $requestedQty
    ) {
        $errors = [];
        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository **/
        $productRepository = $this->productRepositoryInterfaceFactory->create();
        $product = $productRepository->get($sku);
        if (($product->getCustomAttribute('production_item')) && !($product->getCustomAttribute('production_item')->getValue())) {
            $result = $proceed();
        } else {
            $result = $this->productSalableResultFactory->create(['errors' => $errors]);
        }
        return $result;
    }  
}
