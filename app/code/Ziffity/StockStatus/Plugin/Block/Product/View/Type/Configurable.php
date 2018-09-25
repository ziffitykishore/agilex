<?php
declare(strict_types=1);

namespace Ziffity\StockStatus\Plugin\Block\Product\View\Type;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;

class Configurable
{
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * Configurable constructor
     *
     * @param StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * Get All used products for configurable
     *
     * @param Subject $subject
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeGetAllowProducts(
        Subject $subject
    ) {
        if (!$subject->hasAllowProducts() &&
            /*$this->stockConfiguration->isShowOutOfStock()) {
   
            $product = $subject->getProduct();
            $allowProducts = [];
            $usedProducts = $product->getTypeInstance(true)
                ->getUsedProducts($product);
            
            foreach ($usedProducts as $usedProduct) {
                if ($usedProduct->getStatus() == Status::STATUS_ENABLED) {
                    $allowProducts[] = $usedProduct;
                }
            }
            $subject->setAllowProducts($allowProducts);
        }*/
        return $subject->getData('allow_products');
    }
}
