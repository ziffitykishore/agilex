<?php
namespace SomethingDigital\CustomPdp\Pricing\Price;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Group\RetrieverInterface as CustomerGroupRetrieverInterface;

class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{
    private $stockRegistry;

    public function __construct(
        StockRegistryInterface $stockRegistry,
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency,
        Session $customerSession,
        GroupManagementInterface $groupManagement,
        CustomerGroupRetrieverInterface $customerGroupRetriever = null

    ) {
        $this->stockRegistry = $stockRegistry;
        parent::__construct(
            $saleableItem,
            $quantity,
            $calculator,
            $priceCurrency,
            $customerSession,
            $groupManagement,
            $customerGroupRetriever = null
        );
    }

    public function getMinSaleQty()
    {
        $stockItem = $this->stockRegistry->getStockItem($this->product->getId());
        
        return $stockItem->getMinSaleQty();
        return 131;
    }

    public function getBasePrice()
    {
        return parent::getBasePrice();
    }
}